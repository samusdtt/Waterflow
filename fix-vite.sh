#!/bin/bash

# 🛠️ Fix Laravel Vite Manifest Error for Termux Environment
# This script automatically replaces @vite with Laravel Mix helpers
# and rebuilds frontend assets.

echo "🔧 Fixing Laravel Vite Manifest Error..."

# Step 1: Open layout file and replace @vite directive
LAYOUT="resources/views/layouts/app.blade.php"

if grep -q "@vite" "$LAYOUT"; then
  sed -i "s|@vite(['resources/css/app.css', 'resources/js/app.js'])|<link rel=\"stylesheet\" href=\"{{ mix('css/app.css') }}\">\n<script src=\"{{ mix('js/app.js') }}\" defer></script>|g" "$LAYOUT"
  echo "✅ Replaced @vite directive with Laravel Mix tags in $LAYOUT"
else
  echo "⚠️ No @vite directive found — already using Laravel Mix."
fi

# Step 2: Build frontend assets
echo "🏗️ Building frontend assets with Laravel Mix..."
npm run dev

# Step 3: Clear Laravel caches just in case
echo "🧹 Clearing caches..."
php artisan view:clear
php artisan cache:clear
php artisan config:clear

# Step 4: Serve the app
echo "🚀 Starting Laravel server at http://127.0.0.1:8000"
php artisan serve