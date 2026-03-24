# Sử dụng image PHP 8.2 có sẵn Apache
FROM php:8.2-apache

# Cài đặt các thư viện hệ thống cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Cài đặt PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Bật mod_rewrite cho Apache (quan trọng để Laravel chạy route)
RUN a2enmod rewrite

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy code vào thư mục server
WORKDIR /var/www/html
COPY . .

# Cấp quyền cho folder storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Cài đặt dependency
RUN composer install --no-dev --optimize-autoloader

# Đổi root của Apache sang thư mục public của Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql
# Mở port 80
EXPOSE 80