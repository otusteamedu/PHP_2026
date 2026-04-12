init: docker-down-clear docker-build docker-up start
up: docker-up
down: docker-down
restart: down up
start: composer-install wait-elastic create-index


docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-build:
	docker-compose build

create-index:
	docker-compose run --rm php-cli bin/create_index.php

delete-index:
	docker-compose run --rm php-cli bin/delete_index.php

composer-install:
	docker-compose run --rm php-cli composer install

wait-elastic:
	docker compose run --rm php-cli wait-for-it elastic:9200 -t 30
