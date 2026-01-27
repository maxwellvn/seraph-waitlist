FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libssl-dev \
    pkg-config \
    autoconf \
    gcc \
    make \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install zip

# Install MongoDB PHP extension with increased memory limit
# Using --no-interaction to prevent prompts that could hang the build
RUN pecl channel-update pecl.php.net \
    && pecl install mongodb-1.17.3 --configureoptions 'with-mongodb-ssl="openssl"' \
    && docker-php-ext-enable mongodb

# Enable Apache modules
RUN a2enmod rewrite

# Apache config for .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better layer caching
COPY composer.json composer.lock* ./

# Install dependencies (no interaction to prevent prompts)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copy application code
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
