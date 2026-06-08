# Сборка Vue 3 фронтенда
FROM node:20-alpine AS frontend-builder
WORKDIR /app
RUN chown -R node:node /app
COPY --chown=node:node package*.json ./
# 💡 ИСПРАВЛЕНО: Добавлен флаг --no-audit для экономии памяти на диске
RUN npm install --no-audit --progress=false
COPY --chown=node:node . .
RUN npm run build

# Настройка продакшен PHP-окружения
FROM php:8.4-fpm-alpine
WORKDIR /var/www/html

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
