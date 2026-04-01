FROM nginx:1.25.4-alpine AS dev
COPY ./docker/nginx/config/nginx.conf /etc/nginx/nginx.conf
EXPOSE 80

FROM nginx:1.25.4-alpine AS prod
COPY . /var/www/app
COPY ./docker/nginx/config/nginx.conf /etc/nginx/nginx.conf
EXPOSE 80