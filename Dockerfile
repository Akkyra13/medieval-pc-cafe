FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/* \
    npm install

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer package*.json ./

RUN npm install && npm run build

WORKDIR /var/www/html
