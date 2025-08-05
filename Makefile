# Laravel Application Makefile
# Usage: make <command>

.PHONY: help install dev docker-up docker-down docker-build docker-logs docker-shell
.PHONY: migrate fresh seed test build clean logs artisan

# Default target
help:
	@echo "Available commands:"
	@echo "  install     - Install PHP and Node.js dependencies"
	@echo "  dev         - Start development environment (Laravel + Vite)"
	@echo "  docker-up   - Start Docker containers"
	@echo "  docker-down - Stop Docker containers"
	@echo "  docker-build- Start Docker containers with build"
	@echo "  docker-logs - Show Docker container logs"
	@echo "  docker-shell- Access Laravel container shell"
	@echo "  migrate     - Run database migrations"
	@echo "  fresh       - Fresh database with seeding"
	@echo "  seed        - Run database seeders"
	@echo "  test        - Run tests"
	@echo "  build       - Build frontend assets for production"
	@echo "  clean       - Clean cache and temporary files"
	@echo "  logs        - Show Laravel logs"
	@echo "  artisan     - Run artisan command (usage: make artisan cmd='migrate')"

# Install dependencies
install:
	@echo "Installing PHP dependencies..."
	composer install
	@echo "Installing Node.js dependencies..."
	npm install
	@echo "Building frontend assets..."
	npm run build
	@echo "Setup complete!"

# Development environment
dev:
	@echo "Starting development environment..."
	@echo "Laravel will be available at: http://localhost:8000"
	@echo "Vite dev server will be available at: http://localhost:5173"
	@echo "Press Ctrl+C to stop"
	@echo ""
	@echo "Starting Docker containers..."
	docker-compose -f docker-compose.dev.yml up -d
	@echo "Starting Vite development server..."
	npm run dev

# Docker commands
docker-up:
	@echo "Starting Docker containers..."
	docker-compose -f docker-compose.dev.yml up -d

docker-down:
	@echo "Stopping Docker containers..."
	docker-compose -f docker-compose.dev.yml down

docker-build:
	@echo "Building and starting Docker containers..."
	docker-compose -f docker-compose.dev.yml up --build -d

docker-logs:
	@echo "Showing Docker container logs..."
	docker-compose -f docker-compose.dev.yml logs -f

docker-shell:
	@echo "Accessing Laravel container shell..."
	docker exec -it laravel_app_dev sh

# Database commands
migrate:
	@echo "Running database migrations..."
	docker exec -it laravel_app_dev php artisan migrate

fresh:
	@echo "Fresh database with seeding..."
	docker exec -it laravel_app_dev php artisan migrate:fresh --seed

seed:
	@echo "Running database seeders..."
	docker exec -it laravel_app_dev php artisan db:seed

# PostgreSQL commands
postgres-shell:
	@echo "Accessing PostgreSQL shell..."
	docker exec -it laravel_postgres_dev psql -U postgres -d music_library

postgres-logs:
	@echo "Showing PostgreSQL logs..."
	docker logs laravel_postgres_dev

postgres-reset:
	@echo "Resetting PostgreSQL database..."
	docker-compose -f docker-compose.dev.yml down
	docker volume rm mix-searcher_postgres_data
	docker-compose -f docker-compose.dev.yml up -d

# Testing
test:
	@echo "Running tests..."
	docker exec -it laravel_app_dev php artisan test

# Build commands
build:
	@echo "Building frontend assets for production..."
	npm run build

# Maintenance commands
clean:
	@echo "Cleaning cache and temporary files..."
	docker exec -it laravel_app_dev php artisan cache:clear
	docker exec -it laravel_app_dev php artisan config:clear
	docker exec -it laravel_app_dev php artisan route:clear
	docker exec -it laravel_app_dev php artisan view:clear
	@echo "Cache cleared!"

logs:
	@echo "Showing Laravel logs..."
	docker exec -it laravel_app_dev tail -f storage/logs/laravel.log

# Generic artisan command
artisan:
	@if [ -z "$(cmd)" ]; then \
		echo "Usage: make artisan cmd='<artisan-command>'"; \
		echo "Example: make artisan cmd='migrate'"; \
		exit 1; \
	fi
	@echo "Running artisan command: $(cmd)"
	docker exec -it laravel_app_dev php artisan $(cmd)

# Production commands
prod-build:
	@echo "Building production Docker image..."
	docker-compose up --build

prod-down:
	@echo "Stopping production containers..."
	docker-compose down

# Development helpers
status:
	@echo "=== Docker Containers ==="
	docker ps --filter "name=laravel"
	@echo ""
	@echo "=== Laravel Status ==="
	docker exec -it laravel_app_dev php artisan about
	@echo ""
	@echo "=== Vite Status ==="
	@if pgrep -f "npm run dev" > /dev/null; then \
		echo "Vite is running on http://localhost:5173"; \
	else \
		echo "Vite is not running"; \
	fi

# Quick setup for new developers
setup:
	@echo "Setting up development environment..."
	@echo "1. Installing dependencies..."
	make install
	@echo "2. Starting Docker containers..."
	make docker-up
	@echo "3. Running migrations..."
	make migrate
	@echo "4. Starting Vite development server..."
	npm run dev &
	@echo ""
	@echo "Setup complete! Your application is ready."
	@echo "Laravel: http://localhost:8000"
	@echo "Vite: http://localhost:5173"

# Emergency reset
reset:
	@echo "⚠️  WARNING: This will reset everything!"
	@echo "This will:"
	@echo "  - Stop all containers"
	@echo "  - Remove all containers and volumes"
	@echo "  - Clear all caches"
	@echo "  - Reset database"
	@read -p "Are you sure? (y/N): " confirm && [ "$$confirm" = "y" ] || exit 1
	@echo "Resetting everything..."
	docker-compose -f docker-compose.dev.yml down -v
	docker system prune -f
	make clean
	@echo "Reset complete. Run 'make setup' to start fresh." 