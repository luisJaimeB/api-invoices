FROM php:8.2-fpm

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim unzip git curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crea el directorio de trabajo
WORKDIR /var/www

# Copia los archivos del proyecto
COPY . .

# Instala dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Da permisos a los directorios de Laravel
RUN chmod -R 775 storage bootstrap/cache

# Expone el puerto 8000
EXPOSE 8000

# Comando para iniciar Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
