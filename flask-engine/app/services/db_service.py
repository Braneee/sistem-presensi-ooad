import json
import logging
from typing import List, Optional, Dict

import mysql.connector
import numpy as np

from config import Config

logger = logging.getLogger(__name__)


def _get_conn():
    """Buat koneksi baru ke MySQL."""
    return mysql.connector.connect(
        host=Config.DB_HOST,
        port=Config.DB_PORT,
        database=Config.DB_NAME,
        user=Config.DB_USER,
        password=Config.DB_PASS,
        charset='utf8mb4',
        autocommit=False,
        connection_timeout=5,
    )


def get_all_active_embeddings() -> List[Dict]:
    """
    Ambil semua embedding wajah aktif dari tabel faces (join ke students aktif).
    Return: [{'face_id':int, 'student_id':int, 'embedding':np.ndarray}, ...]
    """
    conn   = _get_conn()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute("""
            SELECT
                f.id         AS face_id,
                f.student_id,
                f.embedding
            FROM faces     f
            INNER JOIN students s ON s.id = f.student_id
            WHERE
                f.is_active  = 1
                AND s.is_active = 1
                AND f.embedding IS NOT NULL
                AND s.deleted_at IS NULL
        """)
        rows = cursor.fetchall()

        result = []
        for row in rows:
            try:
                raw = row['embedding']
                if isinstance(raw, (bytes, bytearray)):
                    raw = raw.decode('utf-8')
                embedding_list = json.loads(raw)
                result.append({
                    'face_id':    row['face_id'],
                    'student_id': row['student_id'],
                    'embedding':  np.array(embedding_list, dtype=np.float64),
                })
            except (json.JSONDecodeError, ValueError, TypeError) as e:
                logger.warning(f"Skip corrupt embedding face_id={row['face_id']}: {e}")
                continue

        logger.debug(f"Loaded {len(result)} embeddings from DB")
        return result

    finally:
        cursor.close()
        conn.close()


def update_face_embedding(student_id: int, embedding: list) -> bool:
    """
    Update kolom embedding pada baris faces terbaru milik student_id.
    Dipanggil setelah Laravel meng-INSERT baris faces baru.
    """
    conn   = _get_conn()
    cursor = conn.cursor()

    try:
        cursor.execute("""
            UPDATE faces
            SET embedding = %s
            WHERE student_id = %s
              AND is_active   = 1
            ORDER BY created_at DESC
            LIMIT 1
        """, (json.dumps(embedding), student_id))
        conn.commit()
        updated = cursor.rowcount > 0
        logger.info(f"Embedding updated for student_id={student_id}: {updated}")
        return updated

    except Exception as e:
        conn.rollback()
        logger.error(f"Failed to update embedding: {e}")
        return False

    finally:
        cursor.close()
        conn.close()


def get_student_info(student_id: int) -> Optional[Dict]:
    """Ambil data ringkas mahasiswa."""
    conn   = _get_conn()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute("""
            SELECT s.id, s.nim, s.name, c.name AS class_name
            FROM students s
            LEFT JOIN classes c ON c.id = s.class_id
            WHERE s.id = %s AND s.is_active = 1 AND s.deleted_at IS NULL

        """, (student_id,))
        return cursor.fetchone()

    finally:
        cursor.close()
        conn.close()


def check_db_connection() -> bool:
    """Cek apakah koneksi DB berhasil (untuk health check)."""
    try:
        conn = _get_conn()
        conn.ping(reconnect=False)
        conn.close()
        return True
    except Exception:
        return False
