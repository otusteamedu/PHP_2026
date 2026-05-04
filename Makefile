init: docker-down-clear docker-build docker-up composer-install db-set
up: docker-up
down: docker-down
restart: down up


docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-build:
	docker-compose build

composer-install:
	docker-compose run --rm php-cli composer install

db-set:
	docker-compose exec -T postgres psql -U admin -d mapper -f /app/schema.sql

console-test:
	docker-compose run --rm php-cli php bin/console app:test:mapper
