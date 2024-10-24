FROM php:8.2-apache
WORKDIR /var/www/html
COPY ./src .
RUN mkdir db && chown www-data db
EXPOSE 80