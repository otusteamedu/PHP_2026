FROM nginx:stable-alpine

COPY ./docker/balancer/balancer.local.conf /etc/nginx/conf.d/balancer.local.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
