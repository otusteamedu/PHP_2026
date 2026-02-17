FROM bash:latest

RUN apk add --no-cache dos2unix

WORKDIR /app

COPY sum.sh .
COPY sort_cities.sh .
COPY city_table.txt .
COPY entrypoint.sh .

RUN dos2unix *.sh && chmod +x *.sh

ENTRYPOINT ["bash", "./entrypoint.sh"]