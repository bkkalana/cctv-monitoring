import face_recognition
import cv2
import numpy as np
import os
import json
import requests
import logging
from config import Config
from .utils import download_image, base64_to_image

logger = logging.getLogger(__name__)

class FaceDetector:
    def __init__(self):
        self.known_faces = {}
        self.known_encodings = []
        self.known_names = []
        self.confidence_threshold = Config.FACE_DETECTION_CONFIDENCE
        self.model = Config.FACE_DETECTION_MODEL

        # Load known faces on startup
        self.load_known_faces()

    def load_known_faces(self):
        """Load known faces from the dataset directory"""
        try:
            # Create directory if it doesn't exist
            os.makedirs(Config.KNOWN_FACES_DIR, exist_ok=True)

            # Load face data from JSON file
            data_file = os.path.join(Config.KNOWN_FACES_DIR, 'face_data.json')
            if os.path.exists(data_file):
                with open(data_file, 'r') as f:
                    self.known_faces = json.load(f)

            # Generate encodings
            self.known_encodings = []
            self.known_names = []
            for face_id, face_data in self.known_faces.items():
                if 'encodings' in face_data:
                    self.known_encodings.append(np.array(face_data['encodings']))
                    self.known_names.append(face_id)

            logger.info(f"Loaded {len(self.known_faces)} known faces")
        except Exception as e:
            logger.error(f"Error loading known faces: {str(e)}")

    def save_known_faces(self):
        """Save known faces to the dataset directory"""
        try:
            data_file = os.path.join(Config.KNOWN_FACES_DIR, 'face_data.json')
            with open(data_file, 'w') as f:
                json.dump(self.known_faces, f, indent=2)
        except Exception as e:
            logger.error(f"Error saving known faces: {str(e)}")

    def add_face(self, face_id, name, tag, photo_url):
        """Add a new face to the dataset"""
        try:
            # Download the image
            image_path = os.path.join(Config.KNOWN_FACES_DIR, 'images', f"{face_id}.jpg")
            os.makedirs(os.path.dirname(image_path), exist_ok=True)

            if photo_url.startswith('http'):
                download_image(photo_url, image_path)
            else:
                # Assume it's base64 encoded
                image = base64_to_image(photo_url)
                cv2.imwrite(image_path, image)

            # Load the image and find faces
            image = face_recognition.load_image_file(image_path)
            face_locations = face_recognition.face_locations(image, model=self.model)

            if not face_locations:
                raise ValueError("No faces found in the image")

            # Get encodings for the first face found
            encodings = face_recognition.face_encodings(image, face_locations)[0]

            # Add to known faces
            self.known_faces[face_id] = {
                'name': name,
                'tag': tag,
                'image_path': image_path,
                'encodings': encodings.tolist()
            }

            # Update encodings list
            self.known_encodings.append(encodings)
            self.known_names.append(face_id)

            # Save to disk
            self.save_known_faces()

            logger.info(f"Added new face: {name} (ID: {face_id})")
            return True
        except Exception as e:
            logger.error(f"Error adding face: {str(e)}")
            raise

    def update_face(self, face_id, updates):
        """Update a face in the dataset"""
        if face_id not in self.known_faces:
            raise ValueError(f"Face {face_id} not found")

        try:
            face_data = self.known_faces[face_id]

            # Update name and tag
            if 'name' in updates:
                face_data['name'] = updates['name']
            if 'tag' in updates:
                face_data['tag'] = updates['tag']

            # Update photo if provided
            if 'photo_url' in updates and updates['photo_url']:
                image_path = face_data.get('image_path', os.path.join(Config.KNOWN_FACES_DIR, 'images', f"{face_id}.jpg"))

                if updates['photo_url'].startswith('http'):
                    download_image(updates['photo_url'], image_path)
                else:
                    # Assume it's base64 encoded
                    image = base64_to_image(updates['photo_url'])
                    cv2.imwrite(image_path, image)

                # Regenerate encodings
                image = face_recognition.load_image_file(image_path)
                face_locations = face_recognition.face_locations(image, model=self.model)

                if not face_locations:
                    raise ValueError("No faces found in the updated image")

                encodings = face_recognition.face_encodings(image, face_locations)[0]
                face_data['encodings'] = encodings.tolist()

                # Update encodings list
                if face_id in self.known_names:
                    idx = self.known_names.index(face_id)
                    self.known_encodings[idx] = encodings

            # Save to disk
            self.save_known_faces()

            logger.info(f"Updated face: {face_id}")
            return True
        except Exception as e:
            logger.error(f"Error updating face: {str(e)}")
            raise

    def remove_face(self, face_id):
        """Remove a face from the dataset"""
        if face_id not in self.known_faces:
            raise ValueError(f"Face {face_id} not found")

        try:
            # Remove from encodings list
            if face_id in self.known_names:
                idx = self.known_names.index(face_id)
                del self.known_names[idx]
                del self.known_encodings[idx]

            # Remove image file
            face_data = self.known_faces[face_id]
            if 'image_path' in face_data and os.path.exists(face_data['image_path']):
                os.remove(face_data['image_path'])

            # Remove from known faces
            del self.known_faces[face_id]

            # Save to disk
            self.save_known_faces()

            logger.info(f"Removed face: {face_id}")
            return True
        except Exception as e:
            logger.error(f"Error removing face: {str(e)}")
            raise

    def detect_faces(self, frame):
        """Detect faces in a frame and identify known faces"""
        faces = []

        try:
            # Convert frame from BGR (OpenCV) to RGB (face_recognition)
            rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)

            # Find all face locations and encodings in the current frame
            face_locations = face_recognition.face_locations(rgb_frame, model=self.model)
            face_encodings = face_recognition.face_encodings(rgb_frame, face_locations)

            for (top, right, bottom, left), face_encoding in zip(face_locations, face_encodings):
                # Compare with known faces
                matches = face_recognition.compare_faces(self.known_encodings, face_encoding, tolerance=0.6)
                name = "Unknown"
                face_id = None
                confidence = 0

                if True in matches:
                    # Find the best match
                    face_distances = face_recognition.face_distance(self.known_encodings, face_encoding)
                    best_match_idx = np.argmin(face_distances)
                    confidence = 1 - face_distances[best_match_idx]

                    if confidence >= self.confidence_threshold:
                        face_id = self.known_names[best_match_idx]
                        name = self.known_faces[face_id]['name']

                # Draw box and label
                color = (0, 255, 0) if face_id else (0, 0, 255)
                cv2.rectangle(frame, (left, top), (right, bottom), color, 2)

                label = f"{name} ({confidence:.1%})" if face_id else "Unknown"
                cv2.rectangle(frame, (left, bottom - 25), (right, bottom), color, cv2.FILLED)
                cv2.putText(frame, label, (left + 6, bottom - 6), cv2.FONT_HERSHEY_DUPLEX, 0.5, (255, 255, 255), 1)

                # Add to results
                faces.append({
                    'face_id': face_id,
                    'name': name,
                    'confidence': confidence,
                    'location': (top, right, bottom, left)
                })

                # If unknown face, send alert
                if not face_id:
                    self.send_alert(frame, (top, right, bottom, left))

        except Exception as e:
            logger.error(f"Error detecting faces: {str(e)}")

        return frame, faces

    def send_alert(self, frame, location):
        """Send an alert about an unknown face"""
        try:
            top, right, bottom, left = location
            face_img = frame[top:bottom, left:right]

            # Encode image as base64
            _, buffer = cv2.imencode('.jpg', face_img)
            image_base64 = base64.b64encode(buffer).decode('utf-8')

            # Send to Laravel API
            response = requests.post(
                f"{Config.LARAVEL_API_URL}/alerts",
                json={
                    'camera_id': '1',  # TODO: Get actual camera ID
                    'snapshot': image_base64,
                    'confidence': 0.0
                },
                headers={'Authorization': f'Bearer {Config.API_TOKEN}'}
            )
            response.raise_for_status()

            logger.info("Sent alert about unknown face")
        except Exception as e:
            logger.error(f"Error sending alert: {str(e)}")

    def update_confidence_threshold(self, threshold):
        """Update the confidence threshold for face recognition"""
        self.confidence_threshold = float(threshold)
        logger.info(f"Updated confidence threshold to {self.confidence_threshold}")

    def sync_faces(self):
        """Sync faces with Laravel API"""
        try:
            response = requests.get(
                f"{Config.LARAVEL_API_URL}/faces",
                headers={'Authorization': f'Bearer {Config.API_TOKEN}'}
            )
            response.raise_for_status()

            faces = response.json().get('faces', [])

            # Remove faces not in the API
            for face_id in list(self.known_faces.keys()):
                if not any(f['id'] == face_id for f in faces):
                    self.remove_face(face_id)

            # Add/update faces from the API
            for face_data in faces:
                face_id = str(face_data['id'])
                if face_id in self.known_faces:
                    self.update_face(face_id, {
                        'name': face_data.get('name'),
                        'tag': face_data.get('tag'),
                        'photo_url': face_data.get('photo_url')
                    })
                else:
                    self.add_face(
                        face_id=face_id,
                        name=face_data.get('name'),
                        tag=face_data.get('tag'),
                        photo_url=face_data.get('photo_url')
                    )

            logger.info(f"Synced {len(faces)} faces from Laravel API")
            return True
        except Exception as e:
            logger.error(f"Error syncing faces: {str(e)}")
            return False
