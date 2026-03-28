up:
	docker run -d \
	  --name db-cinema \
	  -e POSTGRES_PASSWORD=12345 \
	  -p 5432:5432 \
	  postgres:latest

down:
	docker stop db-cinema
	docker rm db-cinema
