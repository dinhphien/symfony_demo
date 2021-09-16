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

ARG packages=xdebug
RUN for pkg in $packages; do \ 
        pecl install $pkg; \
        docker-php-ext-enable $pkg; \
    done

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data;

COPY ./.docker/xdebug.ini /usr/local/etc/php/conf.d/