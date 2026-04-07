FROM nginx:latest

COPY ./nginx.conf /etc/nginx/conf.d/customer.conf

WORKDIR /var/www/public  

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]