FROM php:8.3-apache

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions pdo_pgsql intl http sodium gd

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
    mv composer.phar /usr/local/bin/composer

RUN apt update && apt install -yqq && \
    apt-get install -y unzip git

COPY . /var/www

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

COPY docker/php.ini /usr/local/etc/php/php.ini

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www

RUN composer install

RUN mkdir -p resources/assets/photos && \
    chown -R www-data . && \
    chmod -R 775 .

EXPOSE 80

ENTRYPOINT ["./docker/entrypoint.sh"]