# Laravel Application Docker Setup

This document provides instructions for running the Laravel application using Docker.

## Prerequisites

- Docker
- Docker Compose

## Quick Start

### Development Environment

1. **Build and start the development containers:**
   ```bash
   docker-compose -f docker-compose.dev.yml up --build
   ```

2. **Access the application:**
   - Open your browser and navigate to `http://localhost:8000`

3. **Run additional commands inside the container:**
   ```bash
   # Access the app container
   docker exec -it laravel_app_dev sh
   
   # Run artisan commands
   docker exec -it laravel_app_dev php artisan migrate
   docker exec -it laravel_app_dev php artisan db:seed
   
   # Install additional packages
   docker exec -it laravel_app_dev composer require package-name
   docker exec -it laravel_app_dev npm install package-name
   ```

### Production Environment

1. **Build and start the production containers:**
   ```bash
   docker-compose up --build
   ```

2. **Access the application:**
   - Open your browser and navigate to `http://localhost:8000`

## File Structure

```
mix-searcher/
├── Dockerfile              # Production Dockerfile
├── Dockerfile.dev          # Development Dockerfile
├── docker-compose.yml      # Production docker-compose
├── docker-compose.dev.yml  # Development docker-compose
├── nginx.conf              # Nginx configuration
└── .dockerignore           # Docker ignore file
```

## Configuration

### Environment Variables

The application uses the following environment variables (configured in `.env`):

- `APP_NAME`: Application name
- `APP_ENV`: Environment (local, production, etc.)
- `APP_KEY`: Application encryption key
- `APP_DEBUG`: Debug mode (true/false)
- `APP_URL`: Application URL
- `DB_CONNECTION`: Database connection (sqlite, mysql, etc.)
- `DB_DATABASE`: Database name/path

### Database

The application is configured to use SQLite by default. The database file is stored in:
- Local: `database/database.sqlite`
- Docker: `/var/www/html/database/database.sqlite`

## Development Workflow

### Making Code Changes

1. **With development containers running:**
   - Edit files in your local directory
   - Changes are automatically reflected in the container
   - Refresh your browser to see changes

2. **Running artisan commands:**
   ```bash
   docker exec -it laravel_app_dev php artisan migrate
   docker exec -it laravel_app_dev php artisan make:controller MyController
   ```

3. **Installing new packages:**
   ```bash
   # PHP packages
   docker exec -it laravel_app_dev composer require package-name
   
   # Node.js packages
   docker exec -it laravel_app_dev npm install package-name
   docker exec -it laravel_app_dev npm run build
   ```

### Debugging

1. **View logs:**
   ```bash
   docker logs laravel_app_dev
   docker logs laravel_nginx_dev
   ```

2. **Access container shell:**
   ```bash
   docker exec -it laravel_app_dev sh
   ```

3. **Check application status:**
   ```bash
   docker exec -it laravel_app_dev php artisan about
   ```

## Production Deployment

### Building for Production

1. **Build the production image:**
   ```bash
   docker build -t laravel-app:latest .
   ```

2. **Run the production containers:**
   ```bash
   docker-compose up -d
   ```

### Environment Setup

1. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Generate application key:**
   ```bash
   docker exec -it laravel_app php artisan key:generate
   ```

3. **Run migrations:**
   ```bash
   docker exec -it laravel_app php artisan migrate --force
   ```

4. **Optimize for production:**
   ```bash
   docker exec -it laravel_app php artisan config:cache
   docker exec -it laravel_app php artisan route:cache
   docker exec -it laravel_app php artisan view:cache
   ```

## Troubleshooting

### Common Issues

1. **Permission errors:**
   ```bash
   # Fix storage permissions
   docker exec -it laravel_app_dev chmod -R 775 storage
   docker exec -it laravel_app_dev chmod -R 775 bootstrap/cache
   ```

2. **Database connection issues:**
   ```bash
   # Check database file exists
   docker exec -it laravel_app_dev ls -la database/
   
   # Recreate database
   docker exec -it laravel_app_dev php artisan migrate:fresh
   ```

3. **Asset build issues:**
   ```bash
   # Rebuild assets
   docker exec -it laravel_app_dev npm run build
   ```

### Container Management

```bash
# Stop containers
docker-compose down

# Remove containers and volumes
docker-compose down -v

# Rebuild containers
docker-compose up --build

# View running containers
docker ps

# View container logs
docker logs container_name
```

## Security Considerations

- The application runs as a non-root user inside containers
- Sensitive files are excluded via `.dockerignore`
- Security headers are configured in Nginx
- Environment variables should be properly configured for production

## Performance Optimization

- PHP-FPM is configured for optimal performance
- Static assets are cached with appropriate headers
- Laravel caches are enabled in production
- Database queries are optimized with proper indexing

## Monitoring

- Application logs are available in `storage/logs/`
- Container logs can be viewed with `docker logs`
- Health checks can be added to the docker-compose configuration 