FROM php:8.3-fpm

#RUN #echo 'deb http://mirror.yandex.ru/debian/ bookworm main contrib non-free non-free-firmware \
#    deb-src http://mirror.yandex.ru/debian/ bookworm main contrib non-free non-free-firmware \
#    deb http://security.debian.org/debian-security bookworm-security main contrib non-free non-free-firmware \
#    deb-src http://security.debian.org/debian-security bookworm-security main contrib non-free non-free-firmware \
#    deb http://mirror.yandex.ru/debian/ bookworm-updates main contrib non-free non-free-firmware \
#    deb-src http://mirror.yandex.ru/debian/ bookworm-updates main contrib non-free non-free-firmware' > /etc/apt/sources.list

#RUN sed -e '/security/s/^/#/' -i /etc/apt/sources.list | echo "deb http://deb.debian.org/debian-security/ bookworm-security main" | tee -a /etc/apt/sources.list

RUN apt-get update && apt-get install -y \
        git \
        curl \
        wget \
        sudo \
        zip unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        zlib1g-dev \
        libicu-dev \
        g++ \
        libxml2-dev \
        libzip-dev \
        libwebp-dev \
        libfreetype6-dev \
        graphviz \
        libpq-dev \
        nano \
    && apt-get autoremove -y && rm -rf /var/cache/apt

RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql zip exif pcntl

# Загружаем актуальную версию Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Создаём пользователя и группу shaman для приложения Laravel
RUN groupadd -g 1000 shaman
RUN useradd -u 1000 -ms /bin/bash -g shaman shaman

# Меняем пользователя на shaman
USER shaman

CMD ["php-fpm"]
