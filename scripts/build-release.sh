#!/bin/sh
set -e

# Read version from version.json
VERSION=$(php -r 'echo json_decode(file_get_contents("version.json"))->version;')
echo "🚀 Building release package for Lara-CMS v${VERSION}..."

# 1. Build frontend assets
echo "📦 Building production frontend assets..."
if [ -f /etc/alpine-release ]; then
  npm i --no-save @rolldown/binding-linux-x64-musl >/dev/null 2>&1 || true
fi
npm run build

# 2. Install production dependencies
echo "⚡ Preparing production PHP autoloader..."
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Create dist folder & zip archive
mkdir -p dist
ZIP_PATH="dist/lara-cms-v${VERSION}.zip"
echo "📁 Creating release package at ${ZIP_PATH}..."

zip -r "${ZIP_PATH}" . \
  -x "*.git*" \
  -x "node_modules/*" \
  -x ".github/*" \
  -x ".env" \
  -x "tests/*" \
  -x "storage/logs/*" \
  -x "storage/framework/cache/*" \
  -x "storage/framework/sessions/*" \
  -x "storage/framework/views/*" \
  -x "graphify-out/*" \
  -x "dist/*"

# 4. Restore development dependencies
echo "🔄 Restoring development dependencies..."
composer install --no-interaction

echo "✅ Release package successfully created: ${ZIP_PATH}"
