FROM nginx:stable-alpine

COPY ./docker/nginx/n.local.conf /etc/nginx/conf.d/n.local.conf

WORKDIR /var/www/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

# Файлы конфигурации в контейнере /etc/nginx
