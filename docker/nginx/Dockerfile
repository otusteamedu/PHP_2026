FROM nginx:stable-alpine

COPY docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /app

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]