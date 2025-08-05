# Self-hosted CCTV Monitoring System

A complete offline CCTV monitoring system with face detection and recognition.

## Features

- Camera management (USB and IP cameras)
- Real-time streaming with MJPEG
- Face detection and recognition
- Alert system for unknown faces
- Video recording (scheduled and event-based)
- User roles and permissions
- Email notifications
- Daily reports

## Requirements

- Windows PC
- PHP 8.2+
- MySQL or SQLite
- Python 3.10+
- Webcam or IP cameras

## Installation

### Laravel Backend

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install && npm run dev

3. Create and configure .env file
4. Run migrations and seeders:
    ```bash
    php artisan migrate --seed
5. Start development server:
    ```bash
    php artisan serve

### Python Service
1. Install Python 3.10+
2. Install dependencies:
    ```bash
    pip install -r requirements.txt
3. Configure .env file
4. Start the service:
    ```bash
    python main.py

### Production Setup

1. Set up a web server (Apache/Nginx) for Laravel
2. Use NSSM to run Python service as a Windows service
3. Configure MySQL properly
4. Set up proper logging and monitoring

### Usage

1. Access the web interface at http://localhost:8000
2. Log in with default admin credentials
3. Add cameras and known faces
4. Configure settings as needed

### License
### MIT
This completes the implementation of your self-hosted CCTV monitoring system with Laravel and Python. The system provides a comprehensive solution with all the requested features, including camera management, face detection and recognition, alerts, video recording, and user management.

The architecture separates concerns between the Laravel backend (for management, UI, and data storage) and the Python service (for real-time video processing), communicating via REST API. This makes the system modular and easier to maintain.

For production deployment, you'll want to add additional features like:
- Proper error handling and logging
- System monitoring
- Backup procedures
- Performance optimization
- Security hardening

But this implementation provides a solid foundation with all the core functionality you requested.
