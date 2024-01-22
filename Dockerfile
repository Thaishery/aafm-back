# php + composer + symfony. 
# run test + start symfony.
FROM php:8.2-fpm
RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

WORKDIR /usr/local/bin

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/
RUN chmod +x /usr/local/bin/symfony
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer
RUN chmod +x /usr/local/bin/composer

RUN git config --global user.email "gdeb@gdeb.fr" \
    && git config --global user.name "Thaishery"

WORKDIR /var/www

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]

# EXPOSE 9000
# RUN symfony server:start --port=9000 -d
# CMD symfony server:start --port=9000 -d
