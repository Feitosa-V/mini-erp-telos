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

# Instalar o Composer diretamente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do projeto para dentro do container
COPY . .

# Instalar dependências do Laravel
RUN composer install --prefer-dist --no-dev --optimize-autoloader

# Gerar chave de aplicação do Laravel
RUN php artisan key:generate

# Definir permissões mais permissivas para testar
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Garantir que o arquivo de log tenha a permissão adequada
RUN touch /var/www/storage/logs/laravel.log \
    && chown www-data:www-data /var/www/storage/logs/laravel.log \
    && chmod 664 /var/www/storage/logs/laravel.log

# Atualizar o Node.js para uma versão compatível com NPM 11
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Atualizar NPM e corrigir problemas de SSL
RUN npm install -g npm@latest \
    && npm config set strict-ssl false \
    && npm cache clean --force

# Forçar o OpenSSL legacy provider para evitar problemas com SSL
ENV NODE_OPTIONS=--openssl-legacy-provider

# Expor a porta do PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]