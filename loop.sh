#!/usr/bin/env bash
# Loop Engineering Script for Lara CMS
set -e

PROJECT_ID="b320a599-4d99-417f-a9c4-7bda958443d9"
MAX_ITERATIONS=10
ITERATION=1

echo "========================================="
echo " Starting Loop Engineering System "
echo "========================================="

while [ $ITERATION -le $MAX_ITERATIONS ]; do
    echo ""
    echo "--- Iteration $ITERATION / $MAX_ITERATIONS ---"

    # Step 1: Run local Pest tests
    echo "[1/3] Running local Pest PHP tests..."
    if vendor/bin/pest --compact; then
        echo "Local Pest tests: PASSED"
        PEST_OK=true
    else
        echo "Local Pest tests: FAILED"
        PEST_OK=false
    fi

    # Step 2: Run TestSprite tests
    echo "[2/3] Running TestSprite verification tests..."
    if testsprite test run --all --project "$PROJECT_ID"; then
        echo "TestSprite tests: PASSED"
        TESTSPRITE_OK=true
    else
        echo "TestSprite tests: FAILED"
        TESTSPRITE_OK=false
    fi

    # Step 3: Check overall status
    if [ "$PEST_OK" = true ] && [ "$TESTSPRITE_OK" = true ]; then
        echo "========================================="
        echo " SUCCESS! All tests passed."
        echo "========================================="
        git add .
        git commit -m "feat(loop): verified passing changes on iteration $ITERATION" || true
        break
    else
        echo "[3/3] Verification failed on iteration $ITERATION."
    fi

    ITERATION=$((ITERATION + 1))
done

if [ $ITERATION -gt $MAX_ITERATIONS ]; then
    echo "Reached MAX_ITERATIONS ($MAX_ITERATIONS). Stopping loop."
    exit 1
fi
