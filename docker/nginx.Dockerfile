# nginx.Dockerfile
FROM nginx:stable

COPY docker/default.conf /etc/nginx/conf.d/default.conf