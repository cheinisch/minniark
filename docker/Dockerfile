# Dockerfile für ImagePortfolio - Apache + PHP

# Basis: PHP 8.2 + Apache
FROM php:8.2-apache

# Arbeitsverzeichnis setzen
WORKDIR /var/www/html

# PHP Extensions installieren (für Uploads, Thumbnails, EXIF, JSON)
RUN apt-get update && apt-get install -y \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libwebp-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd exif fileinfo pdo_mysql mbstring bcmath

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
