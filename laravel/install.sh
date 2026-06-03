#!/bin/bash
# =============================================================================
# INSTALL SCRIPT — LARAVEL (attendance-system/laravel)
# Jalankan dari root project: bash install-laravel.sh
# =============================================================================

set -e  # Stop on first error

echo ""
echo "╔══════════════════════════════════════════════════════╗"
echo "║   Sistem Presensi — Laravel Installation Script     ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""

# 1. Check requirements
echo "⏳ Checking requirements..."
command -v php  >/dev/null 2>&1 || { echo "❌ PHP not found. Install PHP 8.2+"; exit 1; }
command -v composer >/dev/null 2>&1 || { echo "❌ Composer not found."; exit 1; }
command -v node >/dev/null 2>&1 || { echo "❌ Node.js not found."; exit 1; }
command -v npm  >/dev/null 2>&1 || { echo "❌ npm not found."; exit 1; }
echo "✅ All requirements found"

# 2. Install PHP dependencies
echo ""
echo "⏳ Installing Composer dependencies..."
composer install --no-interaction --prefer-dist

# 3. Setup environment
if [ ! -f .env ]; then
    echo ""
    echo "⏳ Setting up .env file..."
    cp .env.example .env
    php artisan key:generate
    echo "✅ .env created"
else
    echo "✅ .env already exists"
fi

# 4. Install Node dependencies and build assets
echo ""
echo "⏳ Installing Node.js dependencies..."
npm install

echo "⏳ Building frontend assets..."
npm run build

# 5. Run migrations
echo ""
echo "⏳ Running database migrations..."
php artisan migrate:fresh --seed --force

# 6. Storage link
echo ""
echo "⏳ Creating storage symlink..."
php artisan storage:link

# 7. Cache config
echo ""
echo "⏳ Caching configuration..."
php artisan config:cache
php artisan route:cache

echo ""
echo "╔══════════════════════════════════════════════════════╗"
echo "║   ✅ Laravel Installation Complete!                 ║"
echo "╠══════════════════════════════════════════════════════╣"
echo "║  Run: php artisan serve                             ║"
echo "║  URL: http://localhost:8000                         ║"
echo "║                                                      ║"
echo "║  Admin Login:                                        ║"
echo "║    Email:    admin@presensi.id                      ║"
echo "║    Password: password123                            ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""
