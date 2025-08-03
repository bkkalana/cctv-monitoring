import cv2
import requests
import time
import logging
import threading
from config import Config
from .utils import base64_to_image

logger = logging.getLogger(__name__)

class Camera:
    def __init__(self, camera_id, name, camera_type, device_id=None, rtsp_url=None, is_active=True):
        self.camera_id = camera_id
        self.name = name
        self.camera_type = camera_type
        self.device_id = device_id
        self.rtsp_url = rtsp_url
        self.is_active = is_active
        self.is_online = False
        self.capture = None
        self.last_frame = None
        self.lock = threading.Lock()

    def connect(self):
        """Connect to the camera"""
        if not self.is_active:
            return False

        try:
            if self.camera_type == 'usb' and self.device_id:
                self.capture = cv2.VideoCapture(int(self.device_id))
            elif self.camera_type == 'ip' and self.rtsp_url:
                self.capture = cv2.VideoCapture(self.rtsp_url)
            else:
                return False

            if self.capture.isOpened():
                self.is_online = True
                return True
        except Exception as e:
            logger.error(f"Error connecting to camera {self.camera_id}: {str(e)}")

        self.is_online = False
        return False

    def disconnect(self):
        """Disconnect from the camera"""
        with self.lock:
            if self.capture and self.capture.isOpened():
                self.capture.release()
            self.capture = None
            self.is_online = False

    def read_frame(self):
        """Read a frame from the camera"""
        with self.lock:
            if not self.capture or not self.capture.isOpened():
                if not self.connect():
                    return None

            ret, frame = self.capture.read()
            if not ret:
                self.disconnect()
                return None

            self.last_frame = frame
            return frame

    def generate_frames(self, face_detector, video_recorder):
        """Generator function to yield MJPEG frames with face detection"""
        while True:
            frame = self.read_frame()
            if frame is None:
                time.sleep(1)
                continue

            # Perform face detection
            frame, faces = face_detector.detect_faces(frame)

            # Record video if needed
            video_recorder.process_frame(self.camera_id, frame, faces)

            # Encode frame as JPEG
            ret, buffer = cv2.imencode('.jpg', frame)
            frame = buffer.tobytes()

            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

class CameraManager:
    def __init__(self):
        self.cameras = {}
        self.lock = threading.Lock()

    def add_camera(self, camera_id, name, camera_type, device_id=None, rtsp_url=None, is_active=True):
        """Add a new camera"""
        with self.lock:
            camera = Camera(
                camera_id=camera_id,
                name=name,
                camera_type=camera_type,
                device_id=device_id,
                rtsp_url=rtsp_url,
                is_active=is_active
            )
            self.cameras[camera_id] = camera

            # Try to connect
            camera.connect()

    def update_camera(self, camera_id, updates):
        """Update camera settings"""
        with self.lock:
            if camera_id not in self.cameras:
                raise ValueError(f"Camera {camera_id} not found")

            camera = self.cameras[camera_id]

            # Disconnect if currently connected
            if camera.is_online:
                camera.disconnect()

            # Update properties
            for key, value in updates.items():
                if value is not None:
                    setattr(camera, key, value)

            # Reconnect if active
            if camera.is_active:
                camera.connect()

    def remove_camera(self, camera_id):
        """Remove a camera"""
        with self.lock:
            if camera_id not in self.cameras:
                raise ValueError(f"Camera {camera_id} not found")

            camera = self.cameras[camera_id]
            camera.disconnect()
            del self.cameras[camera_id]

    def get_camera(self, camera_id):
        """Get a camera by ID"""
        with self.lock:
            return self.cameras.get(camera_id)

    def sync_cameras(self):
        """Sync cameras with Laravel API"""
        try:
            response = requests.get(
                f"{Config.LARAVEL_API_URL}/cameras",
                headers={'Authorization': f'Bearer {Config.API_TOKEN}'}
            )
            response.raise_for_status()

            cameras = response.json().get('cameras', [])
            with self.lock:
                # Remove cameras not in the API
                for camera_id in list(self.cameras.keys()):
                    if not any(c['id'] == camera_id for c in cameras):
                        self.remove_camera(camera_id)

                # Add/update cameras from the API
                for camera_data in cameras:
                    camera_id = str(camera_data['id'])
                    if camera_id in self.cameras:
                        self.update_camera(camera_id, {
                            'name': camera_data.get('name'),
                            'camera_type': camera_data.get('type'),
                            'device_id': camera_data.get('device_id'),
                            'rtsp_url': camera_data.get('rtsp_url'),
                            'is_active': camera_data.get('is_active', True)
                        })
                    else:
                        self.add_camera(
                            camera_id=camera_id,
                            name=camera_data.get('name'),
                            camera_type=camera_data.get('type'),
                            device_id=camera_data.get('device_id'),
                            rtsp_url=camera_data.get('rtsp_url'),
                            is_active=camera_data.get('is_active', True)
                        )

            logger.info(f"Synced {len(cameras)} cameras from Laravel API")
            return True
        except Exception as e:
            logger.error(f"Error syncing cameras: {str(e)}")
            return False
