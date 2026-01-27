FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libssl-dev \
    pkg-config

# Install PHP extensions
RUN docker-php-ext-install zip

# Install MongoDB PHP extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
