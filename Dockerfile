# Stage 1: Build Frontend Assets
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP Application
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    unzip \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    postgresql-client \
    postgresql-dev \
    curl \
    nginx \
    supervisor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring zip exif pcntl bcmath intl soap xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . .

# Copy frontend assets from node-builder stage
COPY --from=node-builder /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy Entrypoint Script
COPY ./docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 9000 and start application
EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
