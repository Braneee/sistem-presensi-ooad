# 🎓 Sistem Presensi Online — Face Recognition

Sistem presensi otomatis berbasis face recognition dengan arsitektur microservice.
Mahasiswa presensi menggunakan webcam tanpa login atau input manual.

---

## 🏗️ Arsitektur

```
Browser (Webcam)
     │
     │ HTTPS / base64 image
     ▼
Laravel 11 (Port 8000)
  ├── Auth + Session
  ├── Attendance Logic
  ├── FlaskService (HTTP client)
     │
     │ Internal REST API + X-API-Key
     ▼
Python Flask (Port 5000)
  ├── DeepFace ArcFace
  ├── Face Detection & Embedding
  └── Cosine Similarity Matching
     │
     ▼
MySQL Database (shared)
```

---

## ⚡ Quick Start

### 1. Buat Database MySQL

```sql
CREATE DATABASE attendance_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Setup Laravel

```bash
cd laravel/

# Install dependencies
composer install

# Setup environment
cp .env.example .env
# Edit .env: set DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Generate app key
php artisan key:generate

# Jalankan migrasi + seeder
php artisan migrate:fresh --seed

# Storage link
php artisan storage:link

# Build assets (Tailwind via CDN, tidak perlu build untuk development)
# Untuk production: npm install && npm run build

# Start server
php artisan serve
# → http://localhost:8000
```

### 3. Setup Flask Engine

```bash
cd flask-engine/

# Buat virtual environment
python3 -m venv venv
source venv/bin/activate      # Linux/Mac
# venv\Scripts\activate.bat   # Windows

# Install dependencies
pip install -r requirements.txt
# ⚠️ DeepFace model (~100-200MB) akan didownload otomatis saat pertama run

# Setup environment
cp .env .env.backup  # backup
# Edit .env: pastikan DB settings sama dengan Laravel

# Jalankan Flask
python run.py
# → http://localhost:5000
```

### 4. Verifikasi

```bash
# Cek Flask berjalan
curl http://localhost:5000/health

# Expected response:
# {"status":"ok","service":"Flask Face Recognition Engine","model":"ArcFace","database":"ok"}

# Login Laravel Admin
# URL: http://localhost:8000/admin/login
# Email: admin@presensi.id
# Password: password123
```

---

## 📋 Alur Penggunaan

### Setup (Admin)
1. Login ke dashboard: `http://localhost:8000/admin/login`
2. Pastikan AI status **Online** (hijau di header)
3. Buat kelas: Menu tidak perlu (sudah ada dari seeder)
4. Kelola mahasiswa: **Admin → Mahasiswa → Tambah**
5. Daftarkan wajah: **Admin → Registrasi Wajah → Pilih Mahasiswa → Ambil Foto**
6. Buat sesi presensi: **Admin → Sesi Presensi → Buat Sesi Baru**

### Presensi (Mahasiswa)
1. Buka: `http://localhost:8000/attendance`
2. Pilih sesi yang aktif
3. Izinkan akses kamera browser
4. Hadapkan wajah ke kamera
5. Klik **Presensi Sekarang**
6. Tunggu feedback ≤ 3 detik

### Monitoring (Admin)
- Klik **Monitor** di samping sesi → realtime polling 5 detik
- Export **PDF** atau **CSV** kapan saja

---

## 🔑 Default Credentials

| Role      | Email                  | Password      |
|-----------|------------------------|---------------|
| Superadmin | admin@presensi.id     | password123   |
| Operator   | operator@presensi.id  | operator123   |

---

## 🌐 API Endpoints

### Laravel (Public)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `POST` | `/api/attendance/detect` | Presensi face recognition |
| `GET`  | `/api/sessions/{id}/attendance` | Polling data hadir |

#### POST /api/attendance/detect

**Request:**
```json
{
  "session_id": 1,
  "image": "data:image/jpeg;base64,/9j/4AAQ..."
}
```

**Response Sukses (200):**
```json
{
  "success": true,
  "message": "Presensi berhasil! Selamat datang, Andi Firmansyah.",
  "data": {
    "student": { "nim": "2024001", "name": "Andi Firmansyah" },
    "status": "present",
    "checked_in": "09:15:32",
    "similarity": "94.7%"
  }
}
```

**Error Codes:**

| Code | HTTP | Penyebab |
|------|------|----------|
| `SESSION_CLOSED` | 422 | Sesi sudah ditutup |
| `SESSION_NOT_FOUND` | 404 | Session ID tidak ada |
| `FACE_NOT_DETECTED` | 422 | Wajah tidak terdeteksi |
| `FACE_NOT_RECOGNIZED` | 422 | Wajah tidak cocok di DB |
| `DUPLICATE_ATTENDANCE` | 409 | Sudah presensi sesi ini |
| `IMAGE_QUALITY_LOW` | 422 | Gambar buram/gelap |
| `AI_ENGINE_DOWN` | 503 | Flask tidak berjalan |

### Flask (Internal — requires X-API-Key header)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `POST` | `/recognize` | Kenali wajah, return student_id |
| `POST` | `/register-face` | Ekstrak embedding wajah |
| `GET`  | `/health` | Health check |

---

## 🗄️ Database Schema

```
admins          → login admin (superadmin, admin, operator)
classes         → data kelas (TI-A, SI-B, dst)
students        → mahasiswa, FK ke classes
sessions        → sesi presensi per hari, FK ke classes & admins
faces           → embedding wajah (JSON float array), FK ke students
attendances     → rekap hadir, FK ke sessions & students (UNIQUE per sesi)
```

---

## ⚙️ Konfigurasi Face Recognition

Edit `flask-engine/.env`:

```env
# Model lebih akurat tapi lebih lambat:
FACE_MODEL=ArcFace         # Pilihan: Facenet512, VGG-Face
DETECTOR_BACKEND=opencv    # Pilihan: mtcnn (lebih akurat), retinaface (terbaik)
SIMILARITY_THRESHOLD=0.60  # Turunkan = lebih longgar, Naikkan = lebih ketat
```

**Rekomendasi per kebutuhan:**

| Skenario | Model | Detector | Threshold |
|----------|-------|----------|-----------|
| Development (cepat) | ArcFace | opencv | 0.55 |
| Production (balance) | ArcFace | mtcnn | 0.60 |
| High security | Facenet512 | retinaface | 0.70 |

---

## 🛡️ Security Notes

- **API Key** internal Laravel ↔ Flask: ubah `FLASK_API_KEY` di kedua `.env`
- **CSRF** protection aktif di semua form dan AJAX
- **Rate limiting**: `/api/attendance/detect` dibatasi 30 req/menit
- **Password**: bcrypt via Laravel `password` cast
- Captured photos disimpan di `storage/app/public/captures/` (tidak public secara default)

---

## 📁 Struktur Project

```
attendance-system/
├── laravel/                  ← Laravel 11 application
│   ├── app/
│   │   ├── Http/Controllers/ ← Admin/ dan Api/ controllers
│   │   ├── Models/           ← Eloquent models
│   │   ├── Services/         ← FlaskService, AttendanceService, ReportService
│   │   └── Http/Requests/    ← Form validation
│   ├── database/
│   │   ├── migrations/       ← 6 migration files
│   │   └── seeders/          ← Dummy data
│   ├── resources/views/      ← Blade templates
│   └── routes/               ← web.php, api.php
│
└── flask-engine/             ← Python Flask AI engine
    ├── app/
    │   ├── routes/           ← recognize.py, health.py
    │   ├── services/         ← face_service.py, db_service.py
    │   ├── middleware/       ← api_key.py
    │   └── utils/            ← image_utils.py
    ├── config.py
    ├── run.py
    └── requirements.txt
```

---

## 🐛 Troubleshooting

### "AI Engine Offline" di dashboard
```bash
# Pastikan Flask berjalan
cd flask-engine && source venv/bin/activate && python run.py

# Cek port
curl http://localhost:5000/health
```

### "Face not detected" terus-menerus
- Pastikan pencahayaan cukup
- Wajah harus menghadap kamera langsung
- Coba ganti `DETECTOR_BACKEND=mtcnn` di Flask .env (lebih akurat)

### Gagal install deepface (TensorFlow error)
```bash
pip install tensorflow==2.16.0 tf-keras==2.16.0
pip install deepface
```

### Error "UNIQUE constraint" pada attendance
Normal — artinya mahasiswa sudah presensi di sesi ini (duplicate prevention).

### PHP session conflict dengan model Session
Sudah diatasi: model menggunakan `protected $table = 'sessions'`
dan auth guard dikonfigurasi di `config/auth.php`.

---

## 📄 License

MIT — Free to use for educational purposes.
