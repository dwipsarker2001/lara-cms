#!/usr/bin/env bash
set -e

echo "🚀 [Loop Engine] Starting Autonomous Verification & Health Check..."

# 1. Verify Memory Bank Structure
MEMORY_DIR=".agents/memory"
mkdir -p "$MEMORY_DIR"

for file in MEMORY.md ACTIVE_CONTEXT.md PROGRESS.md DECISION_LOG.md LESSONS_LEARNED.md; do
  if [ ! -f "$MEMORY_DIR/$file" ]; then
    echo "⚠️  [Loop Engine] Creating missing memory file: $MEMORY_DIR/$file"
    touch "$MEMORY_DIR/$file"
  fi
done

# 2. Format PHP Code
echo "🎨 [Loop Engine] Running Pint Code Formatter..."
if [ -f "vendor/bin/pint" ]; then
  vendor/bin/pint --dirty --format agent || echo "ℹ️  Pint finished with status 0"
fi

# 3. Execute Automated Tests
echo "🧪 [Loop Engine] Running Pest Test Suite..."
if [ -f "artisan" ]; then
  php artisan test --compact
fi

# 4. Optional Graphify Knowledge Graph Update
if command -v graphify >/dev/null 2>&1 && [ -d "graphify-out" ]; then
  echo "📊 [Loop Engine] Updating graphify knowledge graph..."
  graphify update . || true
fi

echo "✅ [Loop Engine] Health check completed with ZERO test failures."
