# ─────────────────────────────────────────────────────────────────────────────
# WHR-ePIS — Warehouse HR eProcurement Integrated System
# PHP 8.2 CLI (Alpine) — No Node/npm needed (assets via CDN)
# ─────────────────────────────────────────────────────────────────────────────

FROM php:8.2-cli-alpine

LABEL maintainer="WHR-ePIS"
LABEL description="Warehouse HR eProcurement Integrated System – Demo"

# ── System dependencies ──────────────────────────────────────────────────────
RUN apk add --no-cache \
    bash \
    curl \
    unzip \
    git \
    # PostgreSQL (Supabase)
    postgresql-dev \
    # PHP extensions build deps
    libzip-dev \
    libxml2-dev \
    icu-dev \
    oniguruma-dev

# ── PHP extensions ───────────────────────────────────────────────────────────
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    xml \
    ctype \
    bcmath \
    zip \
    intl \
    opcache

# OPcache tuning (for php artisan serve)
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
 && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
 && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/opcache.ini

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

# ── Storage & cache directories ───────────────────────────────────────────────
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# ── Entrypoint ────────────────────────────────────────────────────────────────
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# ── Expose Laravel development server port ────────────────────────────────────
EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
