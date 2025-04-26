# docker/nginx.Dockerfile

FROM nginx:stable

WORKDIR /var/www/html

# App-Dateien ins Image kopieren
COPY . .

RUN mkdir /var/www/html/temp
RUN chown -R www-data:www-data /var/www/html/cache -R
RUN chown -R www-data:www-data /var/www/html/userdata -R
RUN chown -R www-data:www-data /var/www/html/temp -R

# nginx Config überschreiben
COPY docker/default.conf /etc/nginx/conf.d/default.conf
