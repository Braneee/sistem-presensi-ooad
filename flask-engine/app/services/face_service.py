import logging
from typing import Optional, Dict, List

import numpy as np
from deepface import DeepFace

from app.services.db_service import get_all_active_embeddings, update_face_embedding
from app.utils.image_utils import base64_to_numpy, validate_image_quality
from config import Config

logger = logging.getLogger(__name__)


class FaceService:
    """
    Core AI service untuk face detection, embedding extraction,
    similarity matching, dan registration.
    """

    def __init__(self):
        self.model_name       = Config.FACE_MODEL
        self.detector_backend = Config.DETECTOR_BACKEND
        self.threshold        = Config.SIMILARITY_THRESHOLD

    # ──────────────────────────────────────────────────────────────────────────
    # PUBLIC: RECOGNIZE
    # ──────────────────────────────────────────────────────────────────────────

    def recognize(self, image_base64: str) -> Dict:
        """
        Kenali wajah dari base64 image.

        Flow:
          1. Decode base64 → numpy
          2. Validasi kualitas gambar
          3. Extract embedding (DeepFace)
          4. Load semua embeddings dari DB
          5. Cosine similarity matching
          6. Return best match atau error

        Returns dict dengan key: success, student_id, similarity, model, message, code
        """
        try:
            # 1. Decode
            try:
                img = base64_to_numpy(image_base64)
            except ValueError as e:
                return self._err(f'Format gambar tidak valid: {str(e)}', 'INVALID_IMAGE')

            # 2. Quality check
            quality = validate_image_quality(img)
            if not quality['valid']:
                return self._err(quality['reason'], 'IMAGE_QUALITY_LOW')

            # 3. Extract embedding
            embedding = self._extract_embedding(img)
            if embedding is None:
                return self._err(
                    'Wajah tidak terdeteksi. Pastikan wajah menghadap kamera dengan jelas.',
                    'FACE_NOT_DETECTED',
                )

            # 4. Load embeddings DB
            db_records = get_all_active_embeddings()
            if not db_records:
                return self._err(
                    'Tidak ada data wajah terdaftar. Hubungi admin untuk registrasi.',
                    'DATABASE_EMPTY',
                )

            # 5. Matching
            best = self._find_best_match(embedding, db_records)
            if best is None:
                return self._err(
                    'Wajah tidak dikenali. Pastikan Anda terdaftar dalam sistem.',
                    'FACE_NOT_RECOGNIZED',
                )

            logger.info(
                f"[RECOGNIZE] student_id={best['student_id']} "
                f"similarity={best['similarity']:.4f} model={self.model_name}"
            )

            return {
                'success':    True,
                'student_id': int(best['student_id']),
                'similarity': float(best['similarity']),
                'model':      self.model_name,
                'message':    'Wajah berhasil dikenali.',
            }

        except Exception as e:
            logger.error(f"[RECOGNIZE] Unexpected error: {e}", exc_info=True)
            return self._err(f'Kesalahan sistem AI: {str(e)}', 'INTERNAL_ERROR')

    # ──────────────────────────────────────────────────────────────────────────
    # PUBLIC: REGISTER
    # ──────────────────────────────────────────────────────────────────────────

    def register(self, student_id: int, image_base64: str) -> Dict:
        """
        Ekstrak embedding dari foto dan kembalikan ke Laravel.
        Laravel yang menyimpan ke DB; Flask juga update embedding
        langsung jika baris sudah ada.

        Returns dict dengan key: success, embedding (list), model, message
        """
        try:
            # Decode
            try:
                img = base64_to_numpy(image_base64)
            except ValueError as e:
                return self._err(f'Format gambar tidak valid: {str(e)}', 'INVALID_IMAGE')

            # Quality check
            quality = validate_image_quality(img)
            if not quality['valid']:
                return self._err(quality['reason'], 'IMAGE_QUALITY_LOW')

            # Extract
            embedding = self._extract_embedding(img)
            if embedding is None:
                return self._err(
                    'Wajah tidak terdeteksi pada foto yang diunggah. '
                    'Pastikan foto menampilkan wajah yang jelas.',
                    'FACE_NOT_DETECTED',
                )

            embedding_list = embedding.tolist()

            # Coba update embedding di DB jika baris sudah ada (created oleh Laravel)
            # Ini bersifat best-effort; jika gagal, Laravel akan set embedding via JSON response
            update_face_embedding(student_id, embedding_list)

            logger.info(
                f"[REGISTER] student_id={student_id} "
                f"embedding_dim={len(embedding_list)} model={self.model_name}"
            )

            return {
                'success':   True,
                'embedding': embedding_list,
                'model':     self.model_name,
                'message':   'Embedding wajah berhasil diekstrak.',
            }

        except Exception as e:
            logger.error(f"[REGISTER] Unexpected error: {e}", exc_info=True)
            return self._err(f'Gagal mendaftarkan wajah: {str(e)}', 'INTERNAL_ERROR')

    # ──────────────────────────────────────────────────────────────────────────
    # PRIVATE HELPERS
    # ──────────────────────────────────────────────────────────────────────────

    def _extract_embedding(self, img: np.ndarray) -> Optional[np.ndarray]:
        """
        Gunakan DeepFace.represent() untuk ekstrak face embedding.
        Return numpy array atau None jika wajah tidak terdeteksi.
        """
        try:
            results = DeepFace.represent(
                img_path          = img,
                model_name        = self.model_name,
                detector_backend  = self.detector_backend,
                enforce_detection = True,
                align             = True,
            )

            if results and len(results) > 0:
                return np.array(results[0]['embedding'], dtype=np.float64)

            return None

        except ValueError:
            # DeepFace melempar ValueError saat wajah tidak terdeteksi
            return None

        except Exception as e:
            logger.warning(f"[EMBEDDING] Extraction failed: {e}")
            return None

    def _find_best_match(
        self,
        query_emb: np.ndarray,
        db_records: List[Dict],
    ) -> Optional[Dict]:
        """
        Bandingkan embedding query dengan semua embedding di DB.
        Return record dengan similarity tertinggi yang >= threshold, atau None.
        """
        best_score  = -1.0
        best_record = None

        for record in db_records:
            try:
                score = self._cosine_similarity(query_emb, record['embedding'])
            except Exception:
                continue

            if score > best_score:
                best_score  = score
                best_record = record

        if best_record is not None and best_score >= self.threshold:
            return {
                'student_id': best_record['student_id'],
                'face_id':    best_record['face_id'],
                'similarity': best_score,
            }

        if best_record is not None:
            logger.debug(
                f"[MATCH] Best score {best_score:.4f} below threshold {self.threshold} "
                f"(student_id={best_record['student_id']})"
            )

        return None

    @staticmethod
    def _cosine_similarity(a: np.ndarray, b: np.ndarray) -> float:
        """
        Hitung cosine similarity antara dua vektor.
        Return nilai antara -1.0 dan 1.0 (1.0 = identik).
        """
        norm_a = np.linalg.norm(a)
        norm_b = np.linalg.norm(b)

        if norm_a < 1e-10 or norm_b < 1e-10:
            return 0.0

        return float(np.dot(a, b) / (norm_a * norm_b))

    @staticmethod
    def _err(message: str, code: str) -> Dict:
        return {'success': False, 'message': message, 'code': code}
