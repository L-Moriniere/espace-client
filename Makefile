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
	docker exec -it symfony_api php bin/phpunit



.PHONY: up down restart install migrate cache-clear lint phpstan test

