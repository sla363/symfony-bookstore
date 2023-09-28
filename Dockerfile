FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    libpq-dev \
    git \
    && docker-php-ext-install pdo pdo_pgsql gd zip

RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . .

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --prefer-dist --no-scripts --no-dev --no-interaction

RUN php bin/console doctrine:fixtures:load

EXPOSE 80
