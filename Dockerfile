FROM richarvey/nginx-php-fpm:latest

ENV SYMFONY_ENV dev
ENV WEBROOT /var/www/html/web

WORKDIR /var/www/html

COPY . ./

#RUN rm web/app_dev.php
