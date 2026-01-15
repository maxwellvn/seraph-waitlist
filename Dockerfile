FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev

RUN docker-php-ext-install mysqli pdo pdo_mysql zip

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

WORKDIR /var/www/html

RUN if [ -f "composer.json" ]; then composer install --no-dev --optimize-autoloader; fi

RUN chown -R www-data:www-data /var/www/html

# Ensure data directory exists with proper permissions (volume will overlay)
RUN mkdir -p /var/www/html/data && chmod 777 /var/www/html/data

EXPOSE 80
