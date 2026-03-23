FROM richarvey/php-apache-heroku:latest
COPY . /var/www/app
ENV WEBROOT /var/www/app/public
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer install --no-dev --optimize-autoloader