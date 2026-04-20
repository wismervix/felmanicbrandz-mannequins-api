#!/bin/bash
# ============================================
# Laravel Docker Deployment Script
# For DigitalOcean Droplet (Ubuntu 22.04/24.04)
# ============================================

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/var/www/laravel"
DOCKER_COMPOSE_VERSION="v2.24.0"

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# ============================================
# 1. System Update & Dependencies
# ============================================
print_status "Updating system packages..."
sudo apt-get update && sudo apt-get upgrade -y

print_status "Installing required packages..."
sudo apt-get install -y \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg \
    lsb-release \
    git \
    ufw \
    fail2ban

# ============================================
# 2. Install Docker
# ============================================
print_status "Installing Docker..."
if ! command -v docker &> /dev/null; then
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
    echo \
        "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
        $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
    sudo apt-get update
    sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
    sudo usermod -aG docker $USER
    print_status "Docker installed successfully"
else
    print_warning "Docker already installed, skipping..."
fi

# ============================================
# 3. Install Docker Compose
# ============================================
print_status "Installing Docker Compose..."
if ! command -v docker-compose &> /dev/null; then
    sudo curl -L "https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    sudo ln -sf /usr/local/bin/docker-compose /usr/bin/docker-compose
    print_status "Docker Compose installed successfully"
else
    print_warning "Docker Compose already installed, skipping..."
fi

# ============================================
# 4. Configure Firewall
# ============================================
print_status "Configuring UFW firewall..."
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow http
sudo ufw allow https
sudo ufw --force enable

# ============================================
# 5. Setup Application Directory
# ============================================
print_status "Setting up application directory..."
sudo mkdir -p ${APP_DIR}
sudo chown -R $USER:$USER ${APP_DIR}

print_status "Application directory ready at: ${APP_DIR}"
print_warning "Please upload your Laravel project files to: ${APP_DIR}"
print_warning "Then run: cd ${APP_DIR} && docker-compose up -d --build"

# ============================================
# 6. Post-Deploy Helper Commands
# ============================================
cat << 'EOF'

========================================
POST-DEPLOYMENT COMMANDS
========================================

1. Upload your code and .env file:
   scp -r ./* root@your-droplet-ip:/var/www/laravel/

2. Build and start containers:
   cd /var/www/laravel
   docker-compose up -d --build

3. Run Laravel setup:
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate --force
   docker-compose exec app php artisan config:cache
   docker-compose exec app php artisan route:cache
   docker-compose exec app php artisan view:cache

4. Set proper permissions:
   docker-compose exec app chown -R www-data:www-data storage bootstrap/cache

5. Check logs:
   docker-compose logs -f

========================================
EOF

print_status "Server setup complete!"
