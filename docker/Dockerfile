FROM php:8.3-fpm

WORKDIR /var/www/html

COPY . .

RUN mkdir -p /var/www/html/userdata

# Composer installieren und ausführen (falls benötigt)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000

CMD ["php-fpm"]
