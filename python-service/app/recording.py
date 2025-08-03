import cv2
import os
import time
import logging
from datetime import datetime, timedelta
from config import Config

logger = logging.getLogger(__name__)

class VideoRecorder:
    def __init__(self):
        self.recording_sessions = {}
        self.retention_days = Config.VIDEO_RETENTION_DAYS
        self.output_dir = Config.VIDEO_OUTPUT_DIR
        self.fps = Config.VIDEO_FPS
        self.resolution = Config.VIDEO_RESOLUTION

        # Create output directory if it doesn't exist
        os.makedirs(self.output_dir, exist_ok=True)

    def start_recording(self, camera_id):
        """Start a new recording session for a camera"""
        try:
            if camera_id in self.recording_sessions:
                return self.recording_sessions[camera_id]['writer']

            # Create filename with timestamp
            timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
            filename = os.path.join(self.output_dir, f"camera_{camera_id}_{timestamp}.mp4")

            # Create video writer
            fourcc = cv2.VideoWriter_fourcc(*'mp4v')
            writer = cv2.VideoWriter(filename, fourcc, self.fps, self.resolution)

            if not writer.isOpened():
                raise ValueError("Could not open video writer")

            # Store recording session
            self.recording_sessions[camera_id] = {
                'writer': writer,
                'start_time': datetime.now(),
                'filename': filename,
                'trigger': 'scheduled'
            }

            logger.info(f"Started recording for camera {camera_id}")
            return writer
        except Exception as e:
            logger.error(f"Error starting recording: {str(e)}")
            raise

    def stop_recording(self, camera_id):
        """Stop a recording session for a camera"""
        try:
            if camera_id not in self.recording_sessions:
                return

            session = self.recording_sessions[camera_id]
            writer = session['writer']

            # Release writer
            writer.release()

            # Notify Laravel about the recording
            self.notify_laravel(
                camera_id=camera_id,
                file_path=session['filename'],
                start_time=session['start_time'],
                end_time=datetime.now(),
                trigger_type=session['trigger']
            )

            # Remove from active sessions
            del self.recording_sessions[camera_id]

            logger.info(f"Stopped recording for camera {camera_id}")
        except Exception as e:
            logger.error(f"Error stopping recording: {str(e)}")

    def process_frame(self, camera_id, frame, faces):
        """Process a frame for recording"""
        try:
            # Check if we should start/stop recording based on faces
            has_unknown_faces = any(not face['face_id'] for face in faces)

            if has_unknown_faces and camera_id not in self.recording_sessions:
                # Start recording due to unknown face
                writer = self.start_recording(camera_id)
                self.recording_sessions[camera_id]['trigger'] = 'alert'
            elif not has_unknown_faces and camera_id in self.recording_sessions and self.recording_sessions[camera_id]['trigger'] == 'alert':
                # Stop recording if it was triggered by alert and no more unknown faces
                self.stop_recording(camera_id)

            # Write frame if recording
            if camera_id in self.recording_sessions:
                # Resize frame to match recording resolution
                resized_frame = cv2.resize(frame, self.resolution)
                self.recording_sessions[camera_id]['writer'].write(resized_frame)

            # Check for scheduled recordings
            self.check_scheduled_recordings(camera_id)
        except Exception as e:
            logger.error(f"Error processing frame: {str(e)}")

    def check_scheduled_recordings(self, camera_id):
        """Check if scheduled recording should start/stop"""
        try:
            now = datetime.now()

            # Example: Record during business hours (9AM-5PM)
            start_hour = 9
            end_hour = 17

            if start_hour <= now.hour < end_hour:
                if camera_id not in self.recording_sessions:
                    self.start_recording(camera_id)
            else:
                if camera_id in self.recording_sessions and self.recording_sessions[camera_id]['trigger'] == 'scheduled':
                    self.stop_recording(camera_id)
        except Exception as e:
            logger.error(f"Error checking scheduled recordings: {str(e)}")

    def notify_laravel(self, camera_id, file_path, start_time, end_time, trigger_type):
        """Notify Laravel about a completed recording"""
        try:
            response = requests.post(
                f"{Config.LARAVEL_API_URL}/videos",
                json={
                    'camera_id': camera_id,
                    'file_path': file_path,
                    'start_time': start_time.isoformat(),
                    'end_time': end_time.isoformat(),
                    'trigger_type': trigger_type
                },
                headers={'Authorization': f'Bearer {Config.API_TOKEN}'}
            )
            response.raise_for_status()

            logger.info(f"Notified Laravel about recording: {file_path}")
        except Exception as e:
            logger.error(f"Error notifying Laravel: {str(e)}")

    def cleanup_old_recordings(self):
        """Delete old recordings based on retention policy"""
        try:
            cutoff_date = datetime.now() - timedelta(days=self.retention_days)

            for filename in os.listdir(self.output_dir):
                filepath = os.path.join(self.output_dir, filename)
                if os.path.isfile(filepath):
                    file_date = datetime.fromtimestamp(os.path.getmtime(filepath))
                    if file_date < cutoff_date:
                        os.remove(filepath)
                        logger.info(f"Deleted old recording: {filename}")
        except Exception as e:
            logger.error(f"Error cleaning up old recordings: {str(e)}")

    def update_retention_days(self, days):
        """Update the video retention period"""
        self.retention_days = int(days)
        logger.info(f"Updated video retention to {self.retention_days} days")
