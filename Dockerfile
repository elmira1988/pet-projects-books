# Сборка Vue 3 фронтенда
FROM node:20-alpine AS frontend-builder
WORKDIR /app
# 💡 ИСПРАВЛЕНО: Явно выдаем права на рабочую директорию, чтобы npm мог создавать папки
RUN chown -R node:node /app
COPY --chown=node:node package*.json ./
RUN npm install
COPY --chown=node:node . .
RUN npm run build

# Настройка продакшен PHP-окружения
FROM php:8.4-fpm-alpine
WORKDIR /var/www/html

# Используем менеджер apk вместо apt-get для образов Alpine
RUN apk update && apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git \
    bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd bcmath

COPY . .
COPY --from=frontend-builder /app/public/build ./public/build

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache
