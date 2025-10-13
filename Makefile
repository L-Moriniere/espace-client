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

db-test:
	docker exec -it symfony_api php bin/console doctrine:migrations:migrate --no-interaction --env=test

test:
	docker exec -it symfony_api php bin/phpunit

keys:
	docker exec -it symfony_api php bin/console lexik:jwt:generate-keypair

fixtures:
	docker exec -it symfony_api php bin/console doctrine:fixtures:load

users:
	docker exec -it symfony_api php bin/console app:check-connected-users

.PHONY: up down restart install migrate cache-clear lint phpstan test keys fixtures users

