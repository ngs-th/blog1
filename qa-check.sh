#!/bin/bash
# Quality Assurance Pipeline Script
# Runs Pint, PHPStan, and Pest in sequence
# Exits on first failure to ensure all issues are addressed

set -e  # Exit on any command failure

echo "🚀 Starting Quality Assurance Pipeline..."
echo "==========================================="

# Step 1: Laravel Pint (Code Formatting)
echo "🔧 Step 1/3: Running Laravel Pint (Code Formatting)..."
vendor/bin/pint
if [ $? -eq 0 ]; then
    echo "✅ Pint: Code formatting passed"
else
    echo "❌ Pint: Code formatting failed. Please fix formatting issues."
    exit 1
fi
echo ""

# Step 2: PHPStan (Static Analysis)
echo "🔍 Step 2/3: Running PHPStan (Static Analysis)..."
vendor/bin/phpstan analyse app --level=5
if [ $? -eq 0 ]; then
    echo "✅ PHPStan: Static analysis passed"
else
    echo "❌ PHPStan: Static analysis failed. Please fix type errors and missing annotations."
    exit 1
fi
echo ""

# Step 3: Pest (Testing Framework)
echo "🧪 Step 3/3: Running Pest (Testing Framework)..."
php vendor/bin/pest --compact --no-progress --ci 
if [ $? -eq 0 ]; then
    echo "✅ Pest: All tests passed"
else
    echo "❌ Pest: Tests failed. Please fix failing tests."
    exit 1
fi
echo ""

echo "🎉 Quality Assurance Pipeline Completed Successfully!"
echo "==========================================="
echo "✅ Code Formatting: PASSED"
echo "✅ Static Analysis: PASSED"
echo "✅ Test Suite: PASSED"
echo ""
echo "Your code is ready for commit! 🚀"