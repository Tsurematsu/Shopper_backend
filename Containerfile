FROM docker.io/library/php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Configurar el DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html