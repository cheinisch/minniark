# docker/php.Dockerfile

FROM php:8.4-fpm

WORKDIR /var/www/html

# Installiere wichtige PHP-Erweiterungen
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# PHP-FPM läuft auf Port 9000
EXPOSE 9000

CMD ["php-fpm"]
