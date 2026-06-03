from flask import Flask
from flask_cors import CORS

from app.routes.recognize import recognize_bp
from app.routes.health import health_bp


def create_app() -> Flask:
    app = Flask(__name__)

    # Allow requests from Laravel dev server
    CORS(app, origins=[
        'http://localhost:8000',
        'http://127.0.0.1:8000',
        'http://localhost:3000',
    ])

    # Register blueprints
    app.register_blueprint(recognize_bp)
    app.register_blueprint(health_bp)

    return app
