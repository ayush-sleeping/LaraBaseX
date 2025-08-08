#!/usr/bin/env bash
# PHPStan Analysis Script for CI/CD
# Usage: ./scripts/phpstan.sh [options]

set -e

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Default values
MEMORY_LIMIT="2G"
CONFIG_FILE="phpstan.neon"
BASELINE_FILE="phpstan-baseline.neon"
OUTPUT_FORMAT="table"
LEVEL=""
PATHS=""
GENERATE_BASELINE=false
CLEAR_CACHE=false
VERBOSE=false

# Function to display help
show_help() {
    echo "PHPStan Analysis Script for LaraBaseX"
    echo ""
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -h, --help              Show this help message"
    echo "  -m, --memory-limit      Set memory limit (default: 2G)"
    echo "  -c, --config            Configuration file (default: phpstan.neon)"
    echo "  -b, --baseline          Generate baseline file"
    echo "  -l, --level             Analysis level (0-10)"
    echo "  -p, --paths             Specific paths to analyze"
    echo "  -f, --format            Output format (table, checkstyle, json, junit, github)"
    echo "  --clear-cache           Clear result cache before analysis"
    echo "  -v, --verbose           Verbose output"
    echo ""
    echo "Examples:"
    echo "  $0                      Run with default settings"
    echo "  $0 -l 8 -p app/         Analyze app/ directory at level 8"
    echo "  $0 -b                   Generate baseline file"
    echo "  $0 --clear-cache        Clear cache and run analysis"
    echo ""
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -h|--help)
            show_help
            exit 0
            ;;
        -m|--memory-limit)
            MEMORY_LIMIT="$2"
            shift 2
            ;;
        -c|--config)
            CONFIG_FILE="$2"
            shift 2
            ;;
        -b|--baseline)
            GENERATE_BASELINE=true
            shift
            ;;
        -l|--level)
            LEVEL="$2"
            shift 2
            ;;
        -p|--paths)
            PATHS="$2"
            shift 2
            ;;
        -f|--format)
            OUTPUT_FORMAT="$2"
            shift 2
            ;;
        --clear-cache)
            CLEAR_CACHE=true
            shift
            ;;
        -v|--verbose)
            VERBOSE=true
            shift
            ;;
        *)
            echo -e "${RED}Unknown option: $1${NC}"
            show_help
            exit 1
            ;;
    esac
done

# Function to print colored output
print_status() {
    echo -e "${BLUE}[PHPStan]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if PHPStan is installed
if [ ! -f "vendor/bin/phpstan" ]; then
    print_error "PHPStan not found. Please run: composer install"
    exit 1
fi

# Check if config file exists
if [ ! -f "$CONFIG_FILE" ]; then
    print_error "Configuration file $CONFIG_FILE not found"
    exit 1
fi

print_status "Starting PHPStan analysis..."
print_status "Memory limit: $MEMORY_LIMIT"
print_status "Config file: $CONFIG_FILE"

# Clear cache if requested
if [ "$CLEAR_CACHE" = true ]; then
    print_status "Clearing result cache..."
    vendor/bin/phpstan clear-result-cache
fi

# Build PHPStan command
PHPSTAN_CMD="vendor/bin/phpstan analyse --memory-limit=$MEMORY_LIMIT"

if [ "$GENERATE_BASELINE" = true ]; then
    print_status "Generating baseline file..."
    PHPSTAN_CMD="$PHPSTAN_CMD --generate-baseline=$BASELINE_FILE"
else
    PHPSTAN_CMD="$PHPSTAN_CMD --error-format=$OUTPUT_FORMAT"
fi

if [ -n "$LEVEL" ]; then
    PHPSTAN_CMD="$PHPSTAN_CMD --level=$LEVEL"
fi

if [ -n "$PATHS" ]; then
    PHPSTAN_CMD="$PHPSTAN_CMD $PATHS"
fi

if [ "$VERBOSE" = true ]; then
    PHPSTAN_CMD="$PHPSTAN_CMD -v"
fi

# Run PHPStan
print_status "Running: $PHPSTAN_CMD"
echo ""

if eval $PHPSTAN_CMD; then
    if [ "$GENERATE_BASELINE" = true ]; then
        print_success "Baseline file generated: $BASELINE_FILE"
    else
        print_success "PHPStan analysis completed successfully!"
    fi

    # Display statistics if available
    if [ -f ".phpstan-result-cache" ]; then
        print_status "Analysis cached for faster subsequent runs"
    fi

    exit 0
else
    EXIT_CODE=$?
    print_error "PHPStan analysis failed with exit code $EXIT_CODE"

    if [ $EXIT_CODE -eq 1 ]; then
        print_warning "Issues found. Review the output above and fix the problems."
    elif [ $EXIT_CODE -eq 2 ]; then
        print_error "Internal error occurred during analysis"
    fi

    exit $EXIT_CODE
fi
