# syntax=docker/dockerfile:1
# Happy Ecotech CMS — Laravel 13 + Filament v4 + PostgreSQL.
# LƯU Ý: image chưa được build trong môi trường phát triển (Docker daemon tắt) —
# hãy build & kiểm thử trong CI/staging trước khi lên production.

# ---- Stage 1: build asset frontend (Vite) ----
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json vite.config.js ./
RUN npm ci --no-audit --no-fund
COPY resources ./resources
COPY public ./public
RUN npm run build

# ---- Stage 2: PHP application (php-fpm + nginx) ----
FROM serversideup/php:8.3-fpm-nginx

USER root
RUN install-php-extensions pdo_pgsql pgsql intl gd bcmath zip opcache
USER www-data

WORKDIR /var/www/html

# Cài dependency PHP trước để tận dụng cache layer
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --no-progress

# Copy source + asset đã build
COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data --from=assets /app/public/build /var/www/html/public/build

RUN composer dump-autoload --optimize --no-interaction \
    && php artisan storage:link || true

ENV APP_ENV=production \
    APP_DEBUG=false \
    PHP_OPCACHE_ENABLE=1

# serversideup phục vụ public/ qua nginx trên cổng 8080 (chạy non-root).
EXPOSE 8080
