# ─────────────────────────────────────────────────────────────────────────────
# WHR-ePIS — Warehouse HR eProcurement Integrated System
# PHP 8.2 Apache — Production-grade, proven on Azure
# ─────────────────────────────────────────────────────────────────────────────

FROM php:8.2-apache

LABEL maintainer="WHR-ePIS"
LABEL description="Warehouse HR eProcurement Integrated System – Demo"

# ── System dependencies ──────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        xml \
        soap \
        ctype \
        bcmath \
        zip \
        intl \
        gd \
        opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── OPcache tuning ────────────────────────────────────────────────────────────
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
 && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
 && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/opcache.ini

# ── Apache configuration ──────────────────────────────────────────────────────
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# ── Composer ─────────────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Working directory ─────────────────────────────────────────────────────────
WORKDIR /var/www/html

# ── Install PHP dependencies (cached layer) ───────────────────────────────────
# Copy only composer files first so this layer is cached unless deps change
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

# ── Copy application source ───────────────────────────────────────────────────
COPY . .

# ── Post-install: rebuild autoloader with full app code ───────────────────────
RUN composer dump-autoload --optimize --no-interaction

# ── Storage & cache directories + permissions ─────────────────────────────────
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    bootstrap/cache \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

# ── Entrypoint ────────────────────────────────────────────────────────────────
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# ── Expose HTTP port ──────────────────────────────────────────────────────────
EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
