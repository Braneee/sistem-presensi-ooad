import logging

from flask import Blueprint, request, jsonify

from app.middleware.api_key import require_api_key
from app.services.face_service import FaceService

recognize_bp  = Blueprint('recognize', __name__)
face_service  = FaceService()
logger        = logging.getLogger(__name__)


@recognize_bp.route('/recognize', methods=['POST'])
@require_api_key
def recognize():
    """
    POST /recognize
    Kenali wajah dari gambar base64.

    Request JSON:
      { "image": "<base64 data URI>" }

    Response JSON (sukses):
      {
        "success": true,
        "student_id": 5,
        "similarity": 0.9234,
        "model": "ArcFace"
      }

    Response JSON (gagal):
      {
        "success": false,
        "message": "Wajah tidak terdeteksi.",
        "code": "FACE_NOT_DETECTED"
      }
    """
    data = request.get_json(silent=True, force=True)

    if not data:
        return jsonify({
            'success': False,
            'message': 'Request body harus berformat JSON.',
            'code':    'INVALID_CONTENT_TYPE',
        }), 400

    if 'image' not in data or not data['image']:
        return jsonify({
            'success': False,
            'message': 'Field "image" wajib diisi dengan data base64.',
            'code':    'MISSING_IMAGE',
        }), 400

    logger.info('[POST /recognize] Request received')

    result      = face_service.recognize(data['image'])
    status_code = 200 if result.get('success') else 422

    return jsonify(result), status_code


@recognize_bp.route('/register-face', methods=['POST'])
@require_api_key
def register_face():
    """
    POST /register-face
    Ekstrak embedding wajah mahasiswa baru.

    Request JSON:
      { "student_id": 5, "image": "<base64 data URI>" }

    Response JSON (sukses):
      {
        "success": true,
        "embedding": [...],   // array float 512-dim
        "model": "ArcFace"
      }
    """
    data = request.get_json(silent=True, force=True)

    if not data:
        return jsonify({
            'success': False,
            'message': 'Request body harus berformat JSON.',
            'code':    'INVALID_CONTENT_TYPE',
        }), 400

    if 'student_id' not in data or 'image' not in data:
        return jsonify({
            'success': False,
            'message': 'Field "student_id" dan "image" wajib diisi.',
            'code':    'MISSING_FIELDS',
        }), 400

    try:
        student_id = int(data['student_id'])
    except (ValueError, TypeError):
        return jsonify({
            'success': False,
            'message': '"student_id" harus berupa angka integer.',
            'code':    'INVALID_STUDENT_ID',
        }), 400

    if student_id <= 0:
        return jsonify({
            'success': False,
            'message': '"student_id" tidak valid.',
            'code':    'INVALID_STUDENT_ID',
        }), 400

    logger.info(f'[POST /register-face] student_id={student_id}')

    result      = face_service.register(student_id, data['image'])
    status_code = 200 if result.get('success') else 422

    return jsonify(result), status_code
