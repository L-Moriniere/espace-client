# Makefile pour projet Symfony + Docker + Frontend Vue

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose down && docker compose up -d

install:
	composer install
	cd frontend && npm install

migrate:
	php bin/console doctrine:migrations:migrate --no-interaction

cache-clear:
	php bin/console cache:clear

lint:
	php -l src/
	cd frontend && npm run lint

test:
	php bin/phpunit

frontend:
	cd frontend && npm run dev

mailhog:
	@echo "Accès à MailHog : http://localhost:8025"

.PHONY: up down restart install migrate cache-clear lint phpstan test frontend mailhog

