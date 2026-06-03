from flask import Blueprint, jsonify

from app.services.db_service import check_db_connection
from config import Config

health_bp = Blueprint('health', __name__)


@health_bp.route('/health', methods=['GET'])
def health():
    """
    GET /health
    Status check untuk Flask engine dan koneksi database.
    Tidak memerlukan API key (digunakan oleh Laravel untuk monitoring).

    Response:
      {
        "status": "ok",
        "service": "Flask Face Recognition Engine",
        "model": "ArcFace",
        "database": "ok" | "error: ..."
      }
    """
    db_ok = check_db_connection()

    return jsonify({
        'status':   'ok',
        'service':  'Flask Face Recognition Engine',
        'model':    Config.FACE_MODEL,
        'detector': Config.DETECTOR_BACKEND,
        'database': 'ok' if db_ok else 'error: cannot connect',
        'threshold': Config.SIMILARITY_THRESHOLD,
    }), 200
