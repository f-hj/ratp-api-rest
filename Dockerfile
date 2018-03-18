FROM richarvey/nginx-php-fpm:latest

ENV SYMFONY_ENV prod
ENV WEBROOT /var/www/html/web

WORKDIR /var/www/html

COPY . ./