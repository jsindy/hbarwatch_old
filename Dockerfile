FROM php:7.2-apache
RUN apt -y update
RUN apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql
RUN docker-php-ext-install pgsql pdo pdo_pgsql
RUN a2enmod rewrite
COPY . /var/www/html/
