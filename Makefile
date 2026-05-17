init: docker-down-clear docker-build docker-up composer-install
up: docker-up
down: docker-down
restart: down up
check: lint phpcs phpstan


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

index:
	docker-compose run --rm php-cli php bin/index.php

test:
	docker-compose run --rm php-cli composer test

test-coverage:
	docker-compose run --rm php-cli composer test-coverage

phpcs:
	docker-compose run --rm php-cli composer phpcs

phpstan:
	docker-compose run --rm php-cli composer phpstan

lint:
	docker-compose run --rm php-cli composer lint

order:
	docker-compose run --rm php-cli php bin/console app:order
