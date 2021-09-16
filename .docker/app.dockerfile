FROM php:8.0-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    vim

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pcntl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

RUN usermod -u 1000 www-data;