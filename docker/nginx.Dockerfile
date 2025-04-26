FROM nginx:stable

WORKDIR /var/www/html

# Projektdateien ins nginx-Container-Image kopieren
COPY . .

# Standard-Config ersetzen
COPY docker/default.conf /etc/nginx/conf.d/default.conf
