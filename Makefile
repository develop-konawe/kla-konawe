# ==============================================================================
# Portal KLA - Docker & Podman Helper Commands
# ==============================================================================

# Detect container compose tool (prefers podman-compose if podman is installed)
COMPOSE := $(shell which podman-compose 2>/dev/null || which docker-compose 2>/dev/null || echo "docker compose")

.PHONY: help build up down restart logs ps bash migrate seed test clean

help:
	@echo "Available commands (using: $(COMPOSE)):"
	@echo "  make build       - Build container image"
	@echo "  make up          - Start services in background"
	@echo "  make down        - Stop and remove services"
	@echo "  make restart     - Restart all services"
	@echo "  make logs        - Follow container logs"
	@echo "  make ps          - View running containers"
	@echo "  make bash        - Access bash shell in app container"
	@echo "  make migrate     - Run database migrations"
	@echo "  make seed        - Run database seeders"
	@echo "  make optimize    - Run Laravel optimization caches"
	@echo "  make clean       - Remove volumes and temporary files"

build:
	$(COMPOSE) build

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

restart:
	$(COMPOSE) restart

logs:
	$(COMPOSE) logs -f

ps:
	$(COMPOSE) ps

bash:
	$(COMPOSE) exec app sh

migrate:
	$(COMPOSE) exec app php artisan migrate --force

seed:
	$(COMPOSE) exec app php artisan db:seed --force

optimize:
	$(COMPOSE) exec app php artisan optimize:clear
	$(COMPOSE) exec app php artisan optimize

clean:
	$(COMPOSE) down -v

