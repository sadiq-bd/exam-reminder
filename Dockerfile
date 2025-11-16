FROM php:8.2-cli-alpine

RUN apk add --no-cache curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

CMD ["php", "notifier.php"]
