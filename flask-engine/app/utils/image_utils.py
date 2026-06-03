import base64
import re
import numpy as np
import cv2
from PIL import Image
from io import BytesIO


def base64_to_numpy(base64_string: str) -> np.ndarray:
    """
    Decode base64 image string (data URI atau plain) → numpy BGR array (OpenCV format).
    Raises ValueError jika decode gagal.
    """
    # Hapus data URI prefix: "data:image/jpeg;base64,"
    clean = re.sub(r'^data:image/[^;]+;base64,', '', base64_string.strip())

    try:
        img_bytes = base64.b64decode(clean)
    except Exception as e:
        raise ValueError(f"Base64 decode gagal: {str(e)}")

    try:
        pil_img  = Image.open(BytesIO(img_bytes)).convert('RGB')
        np_img   = np.array(pil_img, dtype=np.uint8)
        bgr_img  = cv2.cvtColor(np_img, cv2.COLOR_RGB2BGR)
        return bgr_img
    except Exception as e:
        raise ValueError(f"Konversi gambar gagal: {str(e)}")


def numpy_to_base64(img_array: np.ndarray, quality: int = 90) -> str:
    """
    Encode numpy BGR array → base64 JPEG string.
    """
    _, buffer = cv2.imencode('.jpg', img_array, [cv2.IMWRITE_JPEG_QUALITY, quality])
    return base64.b64encode(buffer).decode('utf-8')


def validate_image_quality(img: np.ndarray) -> dict:
    """
    Validasi kualitas gambar sebelum face recognition.
    Returns dict: { valid: bool, reason: str | None, blur_score: float, brightness: float }
    """
    if img is None or img.size == 0:
        return {'valid': False, 'reason': 'Gambar kosong atau tidak valid.'}

    h, w = img.shape[:2]

    # Cek resolusi minimum
    if w < 100 or h < 100:
        return {
            'valid':  False,
            'reason': f'Resolusi gambar terlalu kecil ({w}x{h}). Minimum 100x100 piksel.',
        }

    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

    # Deteksi blur menggunakan Laplacian variance
    blur_score = float(cv2.Laplacian(gray, cv2.CV_64F).var())
    if blur_score < 40.0:
        return {
            'valid':      False,
            'reason':     'Gambar terlalu buram. Pastikan kamera fokus dan pencahayaan cukup.',
            'blur_score': blur_score,
        }

    # Cek brightness rata-rata
    brightness = float(np.mean(gray))
    if brightness < 35:
        return {
            'valid':      False,
            'reason':     'Gambar terlalu gelap. Perbaiki pencahayaan.',
            'brightness': brightness,
        }
    if brightness > 225:
        return {
            'valid':      False,
            'reason':     'Gambar terlalu terang (overexposed). Kurangi sumber cahaya langsung.',
            'brightness': brightness,
        }

    return {
        'valid':      True,
        'blur_score': blur_score,
        'brightness': brightness,
    }
