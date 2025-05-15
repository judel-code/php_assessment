FROM php:8.2-apache
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip
RUN pecl install mongodb && docker-php-ext-enable mongodb
COPY ./frontend /var/www/html
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
EXPOSE 80