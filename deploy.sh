#!/bin/bash

# Production Deployment Script for aiquiz.vibeai.cv
# This script helps automate the deployment process

set -e  # Exit on error

echo "=================================="
echo "AI Quiz - Production Deployment"
echo "=================================="
echo ""

# Configuration
REMOTE_USER="your_username"  # Change this
REMOTE_HOST="aiquiz.vibeai.cv"
REMOTE_PATH="/var/www/aiquiz.vibeai.cv"
LOCAL_CLIENT_DIST="./client/dist"
LOCAL_API="./api"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Functions
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "ℹ $1"
}

# Check if we're in the right directory
if [ ! -f "package.json" ] && [ ! -d "client" ]; then
    print_error "Please run this script from the project root directory"
    exit 1
fi

# Step 1: Build Frontend
echo ""
print_info "Step 1: Building frontend..."
cd client

if [ ! -d "node_modules" ]; then
    print_warning "node_modules not found. Installing dependencies..."
    npm install
fi

npm run build

if [ $? -eq 0 ]; then
    print_success "Frontend build completed"
else
    print_error "Frontend build failed"
    exit 1
fi

cd ..

# Step 2: Verify build output
echo ""
print_info "Step 2: Verifying build output..."

if [ -f "$LOCAL_CLIENT_DIST/index.html" ]; then
    print_success "Build output verified"
else
    print_error "Build output not found"
    exit 1
fi

# Step 3: Test SSH connection
echo ""
print_info "Step 3: Testing SSH connection..."

if ssh -o ConnectTimeout=5 "$REMOTE_USER@$REMOTE_HOST" "echo 'Connection successful'" > /dev/null 2>&1; then
    print_success "SSH connection successful"
else
    print_error "Cannot connect to server. Please check your SSH credentials."
    exit 1
fi

# Step 4: Backup existing deployment
echo ""
print_info "Step 4: Creating backup on server..."

BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
ssh "$REMOTE_USER@$REMOTE_HOST" << EOF
    if [ -d "$REMOTE_PATH" ]; then
        echo "Creating backup..."
        sudo tar -czf /var/backups/aiquiz_backup_$BACKUP_DATE.tar.gz $REMOTE_PATH 2>/dev/null || true
        echo "Backup created: /var/backups/aiquiz_backup_$BACKUP_DATE.tar.gz"
    fi
EOF

print_success "Backup completed"

# Step 5: Upload frontend files
echo ""
print_info "Step 5: Uploading frontend files..."

scp -r "$LOCAL_CLIENT_DIST"/* "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/public/" > /dev/null 2>&1

if [ $? -eq 0 ]; then
    print_success "Frontend files uploaded"
else
    print_error "Failed to upload frontend files"
    exit 1
fi

# Step 6: Upload API files
echo ""
print_info "Step 6: Uploading API files..."

# Exclude sensitive files
rsync -av --exclude='config.php' --exclude='*.log' --exclude='test*.php' \
    "$LOCAL_API/" "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/api/" > /dev/null 2>&1

if [ $? -eq 0 ]; then
    print_success "API files uploaded"
else
    print_error "Failed to upload API files"
    exit 1
fi

# Step 7: Set permissions
echo ""
print_info "Step 7: Setting file permissions..."

ssh "$REMOTE_USER@$REMOTE_HOST" << EOF
    sudo chown -R www-data:www-data $REMOTE_PATH
    sudo find $REMOTE_PATH -type d -exec chmod 755 {} \;
    sudo find $REMOTE_PATH -type f -exec chmod 644 {} \;
    sudo chmod 755 $REMOTE_PATH/api/*.php
EOF

print_success "Permissions set"

# Step 8: Restart web server
echo ""
print_info "Step 8: Restarting web server..."

ssh "$REMOTE_USER@$REMOTE_HOST" << EOF
    if systemctl is-active --quiet apache2; then
        sudo systemctl restart apache2
        echo "Apache restarted"
    elif systemctl is-active --quiet nginx; then
        sudo systemctl restart nginx
        echo "Nginx restarted"
    fi
EOF

print_success "Web server restarted"

# Step 9: Test deployment
echo ""
print_info "Step 9: Testing deployment..."

HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "https://$REMOTE_HOST")

if [ "$HTTP_CODE" -eq 200 ]; then
    print_success "Website is accessible (HTTP $HTTP_CODE)"
else
    print_warning "Website returned HTTP $HTTP_CODE"
fi

# Summary
echo ""
echo "=================================="
echo "Deployment Summary"
echo "=================================="
echo ""
print_info "Deployment completed at: $(date)"
print_info "Server: $REMOTE_HOST"
print_info "Backup: /var/backups/aiquiz_backup_$BACKUP_DATE.tar.gz"
echo ""
print_success "Deployment successful!"
echo ""
print_info "Next steps:"
echo "  1. Visit https://$REMOTE_HOST to verify"
echo "  2. Test login functionality"
echo "  3. Check error logs if needed"
echo "  4. Monitor server performance"
echo ""
print_warning "Remember to:"
echo "  - Update production config.php with correct credentials"
echo "  - Change default admin password"
echo "  - Verify SSL certificate"
echo ""
