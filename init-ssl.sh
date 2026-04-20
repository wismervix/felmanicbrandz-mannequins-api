#!/bin/bash
# ============================================
# Let's Encrypt SSL Setup Script
# Run this ONCE on the droplet after first deploy
# ============================================

set -e

DOMAIN="api.felmanicbrandz.com"
EMAIL="support@felmanicbrandz.com"
APP_DIR="/var/www/felmanicbrandz-mannequins-api"

echo "=== Installing Certbot ==="
snap install core
snap refresh core
snap install --classic certbot
ln -sf /snap/bin/certbot /usr/bin/certbot

echo "=== Creating certbot webroot ==="
mkdir -p /var/www/certbot

echo "=== Obtaining SSL certificate ==="
certbot certonly \
    --webroot \
    -w /var/www/certbot \
    -d $DOMAIN \
    --agree-tos \
    --non-interactive \
    --email $EMAIL

echo "=== Switching Nginx to SSL config ==="
cd $APP_DIR
mv docker/nginx/default.conf docker/nginx/default-http.conf
mv docker/nginx/default-ssl.conf docker/nginx/default.conf

echo "=== Restarting containers ==="
docker-compose restart nginx

echo "=== Setting up auto-renewal ==="
echo "0 12 * * * certbot renew --quiet && docker-compose -f $APP_DIR/docker-compose.yml restart nginx" | crontab -

echo "=== SSL setup complete ==="
echo "Your API is now available at: https://$DOMAIN"
