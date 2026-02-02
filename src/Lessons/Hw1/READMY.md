## Docker setup 

### Boot up services
Use `docker compose up -d nginx` to boot base containers


### Install composer dependencies
Use `docker compose run --rm composer create-project laravel/laravel .` to install Laravel Framework at empty `src` directory

Or use `docker compose run --rm composer require --dev phpunit/phpunit` to install any composer package
