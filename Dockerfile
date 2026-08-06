FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    curl \
    git \
    unzip \
    libzip-dev \
    zip \
    linux-headers \
    mysql-client \
    gmp-dev \
    bash \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql zip gmp

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000", "--no-reload"]
