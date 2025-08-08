#!/bin/bash

# LaraBaseX API Testing with Newman
# Automated testing script for the Postman collection

echo "🧪 LaraBaseX API Testing with Newman"
echo "===================================="

# Check if Newman is installed
if ! command -v newman &> /dev/null; then
    echo "📦 Newman not found. Installing..."
    npm install -g newman
fi

# Set variables
COLLECTION="postman/LaraBaseX-API-Collection.json"
LOCAL_ENV="postman/LaraBaseX-Local-Environment.json"
PROD_ENV="postman/LaraBaseX-Production-Environment.json"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo ""
echo "📁 Available Test Environments:"
echo "   1. Local Development (localhost:8001)"
echo "   2. Production (configure URL in environment)"
echo ""

read -p "Choose environment (1 for Local, 2 for Production): " choice

case $choice in
    1)
        ENVIRONMENT=$LOCAL_ENV
        ENV_NAME="Local Development"
        ;;
    2)
        ENVIRONMENT=$PROD_ENV
        ENV_NAME="Production"
        ;;
    *)
        echo "❌ Invalid choice. Using Local Development."
        ENVIRONMENT=$LOCAL_ENV
        ENV_NAME="Local Development"
        ;;
esac

echo ""
echo "🚀 Running API tests against $ENV_NAME environment..."
echo "📊 Test results will be saved with timestamp: $TIMESTAMP"

# Create reports directory if it doesn't exist
mkdir -p reports

# Run Newman with multiple reporters
newman run "$COLLECTION" \
    -e "$ENVIRONMENT" \
    --reporters cli,html,json \
    --reporter-html-export "reports/api-test-report-$TIMESTAMP.html" \
    --reporter-json-export "reports/api-test-results-$TIMESTAMP.json" \
    --verbose

# Check exit code
if [ $? -eq 0 ]; then
    echo ""
    echo "✅ All API tests passed successfully!"
    echo "📊 HTML Report: reports/api-test-report-$TIMESTAMP.html"
    echo "📝 JSON Results: reports/api-test-results-$TIMESTAMP.json"
else
    echo ""
    echo "❌ Some API tests failed. Check the reports for details."
    echo "📊 HTML Report: reports/api-test-report-$TIMESTAMP.html"
    echo "📝 JSON Results: reports/api-test-results-$TIMESTAMP.json"
fi

echo ""
echo "🔍 To view HTML report:"
echo "   open reports/api-test-report-$TIMESTAMP.html"
