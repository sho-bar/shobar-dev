FROM wordpress:6.8-php8.3-apache

ENV PATH="./vendor/bin:$PATH"

RUN pecl install xdebug && \
    docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY xdebug.ini /usr/local/etc/php/conf.d
COPY custom.ini $PHP_INI_DIR/conf.d/

RUN apt-get update -y \
    && apt install -y curl libxml2 libxml2-dev nodejs npm \
    && apt clean -y \
    && docker-php-ext-install pdo pdo_mysql soap
