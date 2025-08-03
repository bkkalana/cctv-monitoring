import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

class Config:
    # Flask settings
    FLASK_DEBUG = os.getenv('FLASK_DEBUG', 'False') == 'True'
    SECRET_KEY = os.getenv('SECRET_KEY', 'secret-key')

    # Application settings
    LARAVEL_API_URL = os.getenv('LARAVEL_API_URL', 'http://localhost:8000/api')
    API_TOKEN = os.getenv('API_TOKEN', '')

    # Face detection settings
    FACE_DETECTION_CONFIDENCE = float(os.getenv('FACE_DETECTION_CONFIDENCE', 0.8))
    FACE_DETECTION_MODEL = os.getenv('FACE_DETECTION_MODEL', 'hog')  # or 'cnn'

    # Video recording settings
    VIDEO_RETENTION_DAYS = int(os.getenv('VIDEO_RETENTION_DAYS', 30))
    VIDEO_OUTPUT_DIR = os.getenv('VIDEO_OUTPUT_DIR', 'recordings')
    VIDEO_FPS = int(os.getenv('VIDEO_FPS', 15))
    VIDEO_RESOLUTION = tuple(map(int, os.getenv('VIDEO_RESOLUTION', '640,480').split(',')))

    # Known faces directory
    KNOWN_FACES_DIR = os.getenv('KNOWN_FACES_DIR', 'known_faces')

    # Camera settings
    CAMERA_RECONNECT_DELAY = int(os.getenv('CAMERA_RECONNECT_DELAY', 5))
