import logging
import os

from app import create_app
from config import Config

# ── Logging setup ─────────────────────────────────────────────────────────────
logging.basicConfig(
    level   = logging.DEBUG if Config.DEBUG else logging.INFO,
    format  = '%(asctime)s [%(levelname)s] %(name)s: %(message)s',
    datefmt = '%Y-%m-%d %H:%M:%S',
)

logger = logging.getLogger(__name__)

# ── Create app ────────────────────────────────────────────────────────────────
app = create_app()

if __name__ == '__main__':
    logger.info(f"Starting Flask Face Recognition Engine")
    logger.info(f"  Model:     {Config.FACE_MODEL}")
    logger.info(f"  Detector:  {Config.DETECTOR_BACKEND}")
    logger.info(f"  Threshold: {Config.SIMILARITY_THRESHOLD}")
    logger.info(f"  DB:        {Config.DB_HOST}:{Config.DB_PORT}/{Config.DB_NAME}")
    logger.info(f"  Listening: http://{Config.HOST}:{Config.PORT}")

    app.run(
        host  = Config.HOST,
        port  = Config.PORT,
        debug = Config.DEBUG,
    )
