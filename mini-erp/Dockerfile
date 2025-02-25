FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd pdo pdo_mysql mbstring zip exif pcntl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do projeto para dentro do container
COPY . .

# Instalar dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Definir permissões
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expor a porta do PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]