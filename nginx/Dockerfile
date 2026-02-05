FROM nginx:stable-alpine

COPY ./hosts/mysite.local.conf /etc/nginx/conf.d/default.conf

WORKDIR /data

CMD ["nginx", "-g", "daemon off;"]
