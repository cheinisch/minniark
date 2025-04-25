# Basis: PHP-FPM
FROM php:8.0-fpm

# Setze das Arbeitsverzeichnis
WORKDIR /var/www/html

# Kopiere den Code ins Image
COPY . .

# Erstelle das Flag-File im existierenden /docker Verzeichnis
RUN echo "Image built at $(date)" > /docker/flag.txt

# Rechte setzen (optional, je nach CMS/Projekt)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Standard-Port für PHP-FPM
EXPOSE 9000

# Starte PHP-FPM beim Container-Start
CMD ["php-fpm"]
