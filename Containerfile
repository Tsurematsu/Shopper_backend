FROM docker.io/library/php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Configurar el DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html

WORKDIR /var/www/html

# Copiar archivos de Composer primero (para aprovechar cache de Docker)
COPY ./workspace/composer.json ./workspace/composer.lock* ./

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Actualizar autoload de Composer
RUN composer dump-autoload --optimize

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod 777 /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
