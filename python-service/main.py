from flask import Flask, Response, request, jsonify
from flask_cors import CORS
from app.camera import CameraManager
from app.face_detection import FaceDetector
from app.recording import VideoRecorder
import threading
import time
import logging
from config import Config

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Create Flask application
app = Flask(__name__)
CORS(app)
app.config.from_object(Config)

# Initialize components
camera_manager = CameraManager()
face_detector = FaceDetector()
video_recorder = VideoRecorder()

@app.route('/')
def index():
    return "Python CCTV Service is running"

@app.route('/api/camera/add', methods=['POST'])
def add_camera():
    data = request.json
    camera_id = data.get('camera_id')
    name = data.get('name')
    camera_type = data.get('type')
    device_id = data.get('device_id')
    rtsp_url = data.get('rtsp_url')
    is_active = data.get('is_active', True)

    try:
        camera_manager.add_camera(
            camera_id=camera_id,
            name=name,
            camera_type=camera_type,
            device_id=device_id,
            rtsp_url=rtsp_url,
            is_active=is_active
        )
        return jsonify({'success': True, 'message': 'Camera added successfully'})
    except Exception as e:
        logger.error(f"Error adding camera: {str(e)}")
        return jsonify({'success': False, 'error': str(e)}), 400

@app.route('/api/camera/update', methods=['POST'])
def update_camera():
    data = request.json
    camera_id = data.get('camera_id')
    updates = {
        'name': data.get('name'),
        'camera_type': data.get('type'),
        'device_id': data.get('device_id'),
        'rtsp_url': data.get('rtsp_url'),
        'is_active': data.get('is_active', True)
    }

    try:
        camera_manager.update_camera(camera_id, updates)
        return jsonify({'success': True, 'message': 'Camera updated successfully'})
    except Exception as e:
        logger.error(f"Error updating camera: {str(e)}")
        return jsonify({'success': False, 'error': str(e)}), 400

@app.route('/api/camera/remove', methods=['POST'])
def remove_camera():
    data = request.json
    camera_id = data.get('camera_id')

    try:
        camera_manager.remove_camera(camera_id)
        return jsonify({'success': True, 'message': 'Camera removed successfully'})
    except Exception as e:
        logger.error(f"Error removing camera: {str(e)}")
        return jsonify({'success': False, 'error': str(e)}), 400

@app.route('/api/face/add', methods=['POST'])
def add_face():
    data = request.json
    face_id = data.get('face_id')
    name = data.get('name')
    tag = data.get('tag')
    photo_url = data.get('photo_url')

    try:
        face_detector.add_face(face_id, name, tag, photo_url)
        return jsonify({'success': True, 'message': 'Face added successfully'})
    except Exception as e:
        logger.error(f"Error adding face: {str(e)}")
        return jsonify({'success': False, 'error': str(e)}), 400

@app.route('/api/face/update', methods=['POST'])
def update_face():
    data = request.json
    face_id = data.get('face_id')
    updates = {
        'name': data.get('name'),
        'tag': data.get('tag'),
        'photo_url': data.get('photo_url')
    }

    try:
        face_detector.update_face(face_id, updates)
        return jsonify({'success': True, 'message': 'Face updated successfully'})
    except Exception as e:
        logger.error(f"Error updating face: {str(e)}")
        return jsonify({'success': False, 'error': str(e)}), 400

@app.route('/api/face/remove', methods=['POST'])
def remove_face():
    data = request.json
    face_id = data.get('face_id')

    try:
        face_detector.remove_face(face_id)
        return jsonify({'success': True, 'message': 'Face removed successfully'})
    except Exception as e:
        logger.error(f"Error removing face: {str(e)}")
        return jsonify({'success': False, 'error': str(e)}), 400

@app.route('/api/settings/update', methods=['POST'])
def update_settings():
    data = request.json
    face_detection_confidence = data.get('face_detection_confidence')
    video_retention_days = data.get('video_retention_days')

    try:
        if face_detection_confidence is not None:
            face_detector.update_confidence_threshold(face_detection_confidence)

        if video_retention_days is not None:
            video_recorder.update_retention_days(video_retention_days)

        return jsonify({'success': True, 'message': 'Settings updated successfully'})
    except Exception as e:
        logger.error(f"Error updating settings: {str(e)}")
        return jsonify({'success': False, 'error': str(e)}), 400

@app.route('/stream/<camera_id>')
def video_stream(camera_id):
    """Video streaming route that serves MJPEG frames"""
    try:
        camera = camera_manager.get_camera(camera_id)
        if not camera or not camera.is_active:
            return "Camera not found or inactive", 404

        return Response(
            camera.generate_frames(face_detector, video_recorder),
            mimetype='multipart/x-mixed-replace; boundary=frame'
        )
    except Exception as e:
        logger.error(f"Error in video stream: {str(e)}")
        return str(e), 500

def cleanup_old_videos():
    """Periodic task to clean up old video recordings"""
    while True:
        try:
            video_recorder.cleanup_old_recordings()
        except Exception as e:
            logger.error(f"Error in cleanup task: {str(e)}")
        time.sleep(3600)  # Run every hour

if __name__ == '__main__':
    # Start cleanup thread
    cleanup_thread = threading.Thread(target=cleanup_old_videos, daemon=True)
    cleanup_thread.start()

    # Sync with Laravel on startup
    try:
        camera_manager.sync_cameras()
        face_detector.sync_faces()
    except Exception as e:
        logger.error(f"Initial sync failed: {str(e)}")

    # Start Flask app
    app.run(host='0.0.0.0', port=5000, threaded=True)
