FROM nginx:stable-alpine

COPY ./hosts/mysite.local.conf.template /etc/nginx/templates/default.conf.template

WORKDIR /app

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
