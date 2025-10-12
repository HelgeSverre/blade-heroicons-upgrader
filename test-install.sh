#!/usr/bin/env bash

# Don't exit on errors - we want to test all versions
# set -e

echo "🧪 Testing blade-heroicons-upgrader installation across Laravel versions"
echo "=========================================================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Arrays to track results
declare -a PASSED_VERSIONS
declare -a FAILED_VERSIONS
declare -a TESTED_DIRS

# Create wip directory if it doesn't exist
mkdir -p wip

# Function to test installation
test_laravel_version() {
    local version=$1
    local project_dir="wip/laravel-${version}"
    local test_failed=0

    echo -e "${YELLOW}Testing Laravel ${version}...${NC}"
    echo "----------------------------------------"

    # Remove existing directory if it exists
    if [ -d "$project_dir" ]; then
        echo "Removing existing ${project_dir}"
        rm -rf "$project_dir"
    fi

    # Create Laravel project
    echo "Creating Laravel ${version} project..."
    if ! composer create-project laravel/laravel="${version}.*" "$project_dir" --quiet --no-interaction 2>/dev/null; then
        echo -e "${RED}✗ Failed to create Laravel ${version} project${NC}"
        echo -e "${BLUE}  (This version may not be compatible or available)${NC}"
        FAILED_VERSIONS+=("$version")
        echo ""
        return 1
    fi

    TESTED_DIRS+=("$project_dir")
    cd "$project_dir" || { FAILED_VERSIONS+=("$version"); return 1; }

    # Install the package from local path
    echo "Installing blade-heroicons-upgrader..."
    composer config repositories.local '{"type": "path", "url": "../../"}' --quiet
    if ! composer require --dev helgesverre/blade-heroicons-upgrader:@dev --quiet --no-interaction 2>/dev/null; then
        echo -e "${RED}✗ Failed to install package${NC}"
        cd ../..
        FAILED_VERSIONS+=("$version")
        test_failed=1
    fi

    if [ $test_failed -eq 0 ]; then
        # Verify installation
        if php artisan list | grep -q "blade-heroicons-upgrader:upgrade"; then
            echo -e "${GREEN}✓ Command registered successfully${NC}"
        else
            echo -e "${RED}✗ Command not found${NC}"
            cd ../..
            FAILED_VERSIONS+=("$version")
            test_failed=1
        fi
    fi

    if [ $test_failed -eq 0 ]; then
        # Create a test file with v1 icons
        mkdir -p resources/views
        cat > resources/views/test.blade.php << 'EOF'
<div>
    <x-heroicon-o-archive />
    <x-heroicon-o-database />
    @svg('heroicon-o-upload')
</div>
EOF

        # Run the command in dry-run mode
        echo "Testing command execution (dry-run)..."
        if php artisan blade-heroicons-upgrader:upgrade resources/views/test.blade.php --dry > /dev/null 2>&1; then
            echo -e "${GREEN}✓ Command executed successfully${NC}"
        else
            echo -e "${RED}✗ Command execution failed${NC}"
            cd ../..
            FAILED_VERSIONS+=("$version")
            test_failed=1
        fi
    fi

    # Return to project root
    cd ../..

    if [ $test_failed -eq 0 ]; then
        echo -e "${GREEN}✓ Laravel ${version} test completed successfully${NC}"
        PASSED_VERSIONS+=("$version")
    fi

    echo ""
    return $test_failed
}

# Test each Laravel version
test_laravel_version "10"
test_laravel_version "11"
test_laravel_version "12"

# Print summary
echo ""
echo "=========================================================================="
echo "📊 Test Summary"
echo "=========================================================================="
echo ""

if [ ${#PASSED_VERSIONS[@]} -gt 0 ]; then
    echo -e "${GREEN}✓ Passed (${#PASSED_VERSIONS[@]})${NC}"
    for version in "${PASSED_VERSIONS[@]}"; do
        echo -e "  ${GREEN}●${NC} Laravel ${version}"
    done
    echo ""
fi

if [ ${#FAILED_VERSIONS[@]} -gt 0 ]; then
    echo -e "${RED}✗ Failed (${#FAILED_VERSIONS[@]})${NC}"
    for version in "${FAILED_VERSIONS[@]}"; do
        echo -e "  ${RED}●${NC} Laravel ${version}"
    done
    echo ""
fi

echo "Test projects are located in:"
for dir in "${TESTED_DIRS[@]}"; do
    echo "  - $dir"
done
echo ""
echo "To clean up: rm -rf wip"
echo ""

# Exit with error if any tests failed
if [ ${#FAILED_VERSIONS[@]} -gt 0 ]; then
    exit 1
else
    echo -e "${GREEN}All tests passed!${NC}"
    exit 0
fi
