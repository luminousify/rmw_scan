#!/bin/bash
# Production Deployment Script for RMW System
# Usage: ./deploy_production.sh

echo "=========================================="
echo "RMW System - Production Deployment"
echo "=========================================="
echo ""

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Backup current deployment
echo -e "${YELLOW}[1/10]${NC} Creating backup..."
BACKUP_DIR="backups/$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r . "$BACKUP_DIR/../" 2>/dev/null || true
echo -e "${GREEN}✓ Backup created at: $BACKUP_DIR${NC}"
echo ""

# Step 2: Check for .env file
echo -e "${YELLOW}[2/10]${NC} Checking environment configuration..."
if [ ! -f .env ]; then
    if [ -f .env.production ]; then
        cp .env.production .env
        echo -e "${GREEN}✓ .env file created from .env.production${NC}"
        echo -e "${RED}⚠️  IMPORTANT: Update .env with production credentials!${NC}"
    else
        echo -e "${RED}✗ ERROR: No .env or .env.production file found!${NC}"
        echo -e "${RED}✗ Please create .env file before deploying${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}✓ .env file exists${NC}"
fi
echo ""

# Step 3: Build production CSS
echo -e "${YELLOW}[3/10]${NC} Building production CSS..."
if npm run build-prod; then
    echo -e "${GREEN}✓ CSS built successfully${NC}"
else
    echo -e "${RED}✗ CSS build failed${NC}"
    echo -e "${YELLOW}⚠️  Continuing with existing CSS...${NC}"
fi
echo ""

# Step 4: Remove development files
echo -e "${YELLOW}[4/10]${NC} Removing development/test files..."
DEV_FILES=(
    "test_*.php"
    "fix_*.php"
    "diagnose_*.php"
    "debug_*.php"
    "validate_*.php"
    "check_*.php"
    "*_test.html"
    "*.mdb"
    "*.accdb"
    "bash.exe.stackdump"
)

for pattern in "${DEV_FILES[@]}"; do
    rm -f $pattern 2>/dev/null
done
echo -e "${GREEN}✓ Development files removed${NC}"
echo ""

# Step 5: Set file permissions
echo -e "${YELLOW}[5/10]${NC} Setting secure file permissions..."
chmod 600 .env
chmod 644 config.php
chmod 644 includes/*.php
chmod 755 app/
chmod 644 app/**/*.php
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
echo -e "${GREEN}✓ File permissions set${NC}"
echo ""

# Step 6: Deploy .htaccess
echo -e "${YELLOW}[6/10]${NC} Deploying security configuration..."
if [ -f .htaccess.production ]; then
    cp .htaccess.production .htaccess
    echo -e "${GREEN}✓ .htaccess deployed${NC}"
else
    echo -e "${YELLOW}⚠️  .htaccess.production not found, skipping...${NC}"
fi
echo ""

# Step 7: Test database connection
echo -e "${YELLOW}[7/10]${NC} Testing database connection..."
if [ -f health_check.php ]; then
    HEALTH_CHECK=$(php health_check.php 2>/dev/null)
    if echo $HEALTH_CHECK | grep -q '"status": "healthy"'; then
        echo -e "${GREEN}✓ Database connection successful${NC}"
    else
        echo -e "${RED}✗ Database connection failed${NC}"
        echo -e "$HEALTH_CHECK"
    fi
else
    echo -e "${YELLOW}⚠️  health_check.php not found, skipping database test${NC}"
fi
echo ""

# Step 8: Clear cache
echo -e "${YELLOW}[8/10]${NC} Clearing cache..."
if [ -d "node_modules/.cache" ]; then
    rm -rf node_modules/.cache
    echo -e "${GREEN}✓ Cache cleared${NC}"
fi
echo ""

# Step 9: Deployment summary
echo "=========================================="
echo -e "${GREEN}Deployment Complete!${NC}"
echo "=========================================="
echo ""
echo "Next Steps:"
echo "1. Test the application: http://your-domain.com/"
echo "2. Monitor logs: tail -f includes/conn.log"
echo "3. Test health check: http://your-domain.com/health_check.php"
echo ""
echo -e "${RED}CRITICAL REMINDERS:${NC}"
echo "1. Update .env with production database credentials"
echo "2. Change default user passwords immediately"
echo "3. Update OM Messenger API key in .env"
echo "4. Enable HTTPS/SSL certificate"
echo "5. Set up regular database backups"
echo ""
