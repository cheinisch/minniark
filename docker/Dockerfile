# Basis: PHP-FPM
FROM php:8.0-fpm

WORKDIR /var/www/html

# Projekt ins Image kopieren
COPY . .

# Sicherstellen, dass /docker existiert
RUN ls
#RUN mkdir -p /docker

# Jetzt das Flag-File erzeugen
RUN echo "Image built at $(date)" > ./docker/is_docker.txt

# Rechte setzen (optional, je nach CMS/Projekt)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exponiere Port für PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]