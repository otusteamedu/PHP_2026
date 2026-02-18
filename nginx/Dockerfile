FROM nginx:latest

COPY ./customer.conf /etc/nginx/conf.d/customer.conf

WORKDIR /var/www/html  

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]