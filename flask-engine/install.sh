#!/bin/bash
# =============================================================================
# INSTALL SCRIPT — PYTHON FLASK (attendance-system/flask-engine)
# Jalankan dari folder flask-engine: bash install.sh
# =============================================================================

set -e

echo ""
echo "╔══════════════════════════════════════════════════════╗"
echo "║   Sistem Presensi — Flask Installation Script       ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""

# 1. Check Python
command -v python3 >/dev/null 2>&1 || { echo "❌ Python 3 not found."; exit 1; }
echo "✅ Python: $(python3 --version)"

# 2. Create virtual environment
echo ""
echo "⏳ Creating virtual environment..."
python3 -m venv venv
echo "✅ Virtual environment created"

# 3. Activate venv
echo ""
echo "⏳ Activating virtual environment..."
source venv/bin/activate

# 4. Upgrade pip
echo "⏳ Upgrading pip..."
pip install --upgrade pip -q

# 5. Install dependencies
echo ""
echo "⏳ Installing Python packages (this may take a few minutes)..."
echo "   Note: DeepFace model (~100MB) will be downloaded on first run."
pip install -r requirements.txt

# 6. Setup .env
if [ ! -f .env ]; then
    echo ""
    echo "⏳ Setting up .env..."
    cp .env.example .env 2>/dev/null || echo "# Edit this file" > .env
    echo "✅ .env created — please edit DB settings"
else
    echo "✅ .env already exists"
fi

echo ""
echo "╔══════════════════════════════════════════════════════╗"
echo "║   ✅ Flask Installation Complete!                   ║"
echo "╠══════════════════════════════════════════════════════╣"
echo "║  Activate venv:  source venv/bin/activate           ║"
echo "║  Run:            python run.py                      ║"
echo "║  Health check:   curl http://localhost:5000/health  ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""
echo "⚠️  IMPORTANT: Edit flask-engine/.env with correct DB settings"
echo "   matching your Laravel .env database configuration."
echo ""
