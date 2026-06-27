init: copy-env docker-down-clear docker-build docker-up composer-install
up: docker-up
down: docker-down
restart: down up
check: lint cs-check phpstan test

copy-env:
	[ -f .env.local ] || cp .env .env.local

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

consume:
	docker-compose run --rm php-cli php bin/consume.php

publish:
	docker-compose run --rm php-cli php bin/publish.php $(EMAIL)

test:
	docker-compose run --rm php-cli composer test

test-coverage:
	docker-compose run --rm php-cli composer test-coverage

cs-check:
	docker-compose run --rm php-cli composer cs-check

cs-fix:
	docker-compose run --rm php-cli composer cs-fix

phpstan:
	docker-compose run --rm php-cli composer phpstan

lint:
	docker-compose run --rm php-cli composer lint
