up:
	docker run -d \
	  --name db-cinema \
	  -e MYSQL_ROOT_PASSWORD=12345 \
	  -p 3306:3306 \
	  mysql:latest

down:
	docker stop db-cinema
	docker rm db-cinema
