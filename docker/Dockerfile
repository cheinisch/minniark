# Dockerfile für ImagePortfolio - Apache + PHP

# Basis: PHP 8.2 + Apache
FROM php:8.2-apache

# Arbeitsverzeichnis setzen
WORKDIR /var/www/html

# PHP Extensions installieren (für Uploads, Thumbnails, EXIF, JSON)
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

# Apache-Configs anpassen (Rewrite Engine an für saubere URLs, falls du willst)
RUN a2enmod rewrite headers

# (Optional) .htaccess Nutzung erlauben
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Port freigeben
EXPOSE 80

# App-Dateien ins Image kopieren
COPY . .

# Rechte setzen (optional je nach Projektstruktur)
RUN chown -R www-data:www-data /var/www/html

# Apache startet automatisch beim Containerstart
CMD ["apache2-foreground"]
