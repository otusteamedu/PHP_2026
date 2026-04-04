init: docker-down-clear docker-build docker-up
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

cli:
	docker-compose run --rm php-cli $(ARGS)

script:
	docker-compose run --rm php-cli ./app/bin/index.php $(ARGS)
