FROM php:8.3-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install tesseract-ocr-por
RUN apt-get update --fix-missing && \
    apt-get install -y python3-launchpadlib software-properties-common && \
    rm -rf /var/lib/apt/lists/* && \
    add-apt-repository ppa:alex-p/tesseract-ocr && \
    apt-get install -y tesseract-ocr-por && \
    apt-get clean

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    apt-utils \
    supervisor \
    zip \
    unzip \
    nano \
    vim \
    libnss3 \
    libgbm-dev \
    libasound2 \
    ghostscript \
    libmagickwand-dev --no-install-recommends \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install zip \
    && apt -y autoremove \
    && apt-get clean

RUN docker-php-ext-install soap

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u "$uid" -d /home/"$user" "$user"
RUN mkdir -p /home/"$user"/.composer && \
    chown -R "$user":"$user" /home/"$user"

# Set working directory
WORKDIR /var/www

USER $user
