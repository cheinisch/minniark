# docker/nginx.Dockerfile

FROM nginx:stable

WORKDIR /var/www/html

# App-Dateien ins Image kopieren
COPY . .

# nginx Config überschreiben
COPY docker/default.conf /etc/nginx/conf.d/default.conf
