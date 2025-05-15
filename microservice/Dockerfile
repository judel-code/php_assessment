FROM php:8.2-fpm
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip
RUN pecl install mongodb && docker-php-ext-enable mongodb
WORKDIR /app
COPY . /app
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install
EXPOSE 9000
CMD ["php", "-S", "0.0.0.0:9000", "-t", "public"]
