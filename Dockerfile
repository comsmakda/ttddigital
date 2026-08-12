# ============================================================
# TTD Digital - COM SMKN 2 Pinrang
# Dockerfile siap deploy (Coolify / Docker generik)
# ============================================================

FROM php:8.2-apache

# --- System deps + PHP extensions ---
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        unzip \
        git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd pdo pdo_mysql zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files dulu supaya layer cache composer install efisien
COPY composer.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

# Copy seluruh source code
COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/qrcodes storage/logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage

# --- Apache: arahkan DocumentRoot ke /public ---
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80

CMD ["apache2-foreground"]
