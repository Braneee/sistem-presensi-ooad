import os
from dotenv import load_dotenv

load_dotenv()


class Config:
    # ── Server ────────────────────────────────────────────────────────────────
    HOST  = os.getenv('FLASK_HOST', '0.0.0.0')
    PORT  = int(os.getenv('FLASK_PORT', 5000))
    DEBUG = os.getenv('FLASK_DEBUG', 'false').lower() == 'true'

    # ── Security ──────────────────────────────────────────────────────────────
    API_KEY = os.getenv('FLASK_API_KEY', 'supersecret-internal-api-key-change-this')

    # ── Database (same MySQL as Laravel) ─────────────────────────────────────
    DB_HOST = os.getenv('DB_HOST', '127.0.0.1')
    DB_PORT = int(os.getenv('DB_PORT', 3306))
    DB_NAME = os.getenv('DB_DATABASE', 'attendance_db')
    DB_USER = os.getenv('DB_USERNAME', 'root')
    DB_PASS = os.getenv('DB_PASSWORD', '')

    # ── Face Recognition ─────────────────────────────────────────────────────
    FACE_MODEL           = os.getenv('FACE_MODEL', 'ArcFace')
    DETECTOR_BACKEND     = os.getenv('DETECTOR_BACKEND', 'opencv')
    SIMILARITY_THRESHOLD = float(os.getenv('SIMILARITY_THRESHOLD', '0.60'))
    DISTANCE_METRIC      = os.getenv('DISTANCE_METRIC', 'cosine')

    # ── Storage ───────────────────────────────────────────────────────────────
    UPLOAD_FOLDER = os.getenv('UPLOAD_FOLDER', '/tmp/attendance_uploads')
