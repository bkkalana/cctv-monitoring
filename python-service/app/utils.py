import cv2
import numpy as np
import requests
import os
import logging

logger = logging.getLogger(__name__)

def download_image(url, save_path):
    """Download an image from a URL and save it to disk"""
    try:
        response = requests.get(url, stream=True)
        response.raise_for_status()

        with open(save_path, 'wb') as f:
            for chunk in response.iter_content(1024):
                f.write(chunk)

        return True
    except Exception as e:
        logger.error(f"Error downloading image: {str(e)}")
        raise

def base64_to_image(base64_str):
    """Convert a base64 string to an OpenCV image"""
    try:
        img_bytes = base64.b64decode(base64_str)
        img_array = np.frombuffer(img_bytes, dtype=np.uint8)
        return cv2.imdecode(img_array, cv2.IMREAD_COLOR)
    except Exception as e:
        logger.error(f"Error converting base64 to image: {str(e)}")
        raise
