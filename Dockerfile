FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libxpm-dev \
    libicu-dev \
    git \
    unzip \
    libsodium-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

    # Create user with the same UID/GID as WSL
RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql intl bcmath sodium gd zip exif 

# Creazione dei file di configurazione per PHP
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/docker-fpm.ini && \
    echo "extension=gd.so" > /usr/local/etc/php/conf.d/docker-php-ext-gd.ini && \
    echo "extension=intl.so" > /usr/local/etc/php/conf.d/docker-php-ext-intl.ini && \
    echo "extension=pdo_mysql.so" > /usr/local/etc/php/conf.d/docker-php-ext-pdo_mysql.ini && \
    echo "extension=sodium.so" > /usr/local/etc/php/conf.d/docker-php-ext-sodium.ini && \
    echo "extension=zip.so" > /usr/local/etc/php/conf.d/docker-php-ext-zip.ini

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
&& apt-get install -y nodejs \
&& npm install -g npm

# Installazione di Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/public

COPY docker-configs/nginx/app.conf /etc/nginx/conf.d/default.conf
COPY . .

# Set permissions
RUN chown -R www:www /var/www/html

USER www

EXPOSE 80

CMD ["php-fpm"]