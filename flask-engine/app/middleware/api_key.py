from functools import wraps
from flask import request, jsonify
from config import Config


def require_api_key(f):
    """
    Decorator: validasi X-API-Key header.
    Semua endpoint Flask dilindungi middleware ini.
    """
    @wraps(f)
    def decorated(*args, **kwargs):
        key = request.headers.get('X-API-Key', '').strip()

        if not key:
            return jsonify({
                'success': False,
                'message': 'API key diperlukan. Sertakan header X-API-Key.',
                'code':    'MISSING_API_KEY',
            }), 401

        if key != Config.API_KEY:
            return jsonify({
                'success': False,
                'message': 'API key tidak valid.',
                'code':    'INVALID_API_KEY',
            }), 401

        return f(*args, **kwargs)

    return decorated
