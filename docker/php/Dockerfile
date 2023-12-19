FROM php:8.1-fpm-alpine

WORKDIR /var/www/html

# Install dependencies
RUN apk update && apk add --no-cache \
    wget \
    git \
    curl \
    postgresql-dev \
    oniguruma-dev

# Install extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

# Create .env file
RUN cp .env.example .env

# Install composer dependencies
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-scripts \
    --prefer-dist