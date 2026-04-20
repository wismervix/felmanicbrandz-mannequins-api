# Docker + CI/CD Setup Guide

## Architecture

```
GitHub Push → GitHub Actions Build → Docker Hub → SSH Deploy → DO Droplet
```

## 1. GitHub Secrets Required

Go to **Settings → Secrets and variables → Actions → Repository secrets**

| Secret | Value | How to get |
|--------|-------|------------|
| `DOCKERHUB_USERNAME` | Your Docker Hub username | dockerhub.com |
| `DOCKERHUB_TOKEN` | Docker Hub access token | Account Settings → Security → New Access Token |
| `DOCKERHUB_REPO` | Repository name, e.g. `laravel-app` | The repo you create on Docker Hub |
| `DROPLET_IP` | Your DigitalOcean droplet IP | DO Dashboard |
| `DROPLET_USER` | SSH username (usually `root`) | - |
| `DROPLET_SSH_KEY` | Private SSH key contents | `cat ~/.ssh/id_rsa` (the private key) |

## 2. Docker Hub Setup

1. Create a repository at [hub.docker.com](https://hub.docker.com)
2. Go to **Account Settings → Security → New Access Token**
3. Copy the token and save it as `DOCKERHUB_TOKEN` in GitHub secrets

## 3. Droplet Setup (One-Time)

SSH into your droplet and run:

```bash
# 1. Install Docker
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
newgrp docker

# 2. Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# 3. Create app directory
sudo mkdir -p /var/www/laravel
sudo chown -R $USER:$USER /var/www/laravel

# 4. Upload config files (from local machine)
# scp docker-compose.prod.yml docker/nginx docker/php docker/mysql .env root@DROPLET_IP:/var/www/laravel/

# 5. On droplet, rename prod compose file
cd /var/www/laravel
mv docker-compose.prod.yml docker-compose.yml

# 6. Login to Docker Hub (one time)
docker login
# Enter your Docker Hub username and access token
```

## 4. First Deploy

After pushing to `main` branch, GitHub Actions will:

1. **Build** the Docker image
2. **Push** to Docker Hub
3. **SSH** into your droplet
4. **Pull** the latest image
5. **Restart** containers
6. **Run** Laravel optimizations

## 5. Local Development

Use the original `docker-compose.yml` (with `build:` context):

```bash
docker-compose up -d --build
```

## 6. Production on Droplet

The droplet uses `docker-compose.prod.yml` (renamed to `docker-compose.yml`) which pulls the pre-built image instead of building locally.

## Workflows

| Workflow | Trigger | Purpose |
|----------|---------|---------|
| `docker-build-push.yml` | Push to `main` | Builds & pushes image to Docker Hub |
| `deploy-to-droplet.yml` | After build succeeds | SSH deploys to DO droplet |
