FROM phpdockerio/php:8.2-fpm
ARG PHP_V
ENV PHP_V="$PHP_V"
ENV TERM='xterm-256color'

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update && apt-get -y dist-upgrade\
   && apt-get -y install apt-utils locales git mc iputils-ping\
   && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install PHP with all dependency modules
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php${PHP_V} \
        php${PHP_V}-bcmath  \
        php${PHP_V}-pgsql  \
        php${PHP_V}-mysql  \
        php${PHP_V}-curl  \
        php${PHP_V}-gd  \
        php${PHP_V}-mbstring  \
        php${PHP_V}-intl  \
        php${PHP_V}-xml  \
        php${PHP_V}-zip \
        php${PHP_V}-redis  \
        php${PHP_V}-imagick  \
        php${PHP_V}-xdebug \
        php${PHP_V}-sqlite3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN apt-get update && apt-get install -y \
    curl libxrender1 libfontconfig libxtst6 xz-utils \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

# Permission fix
RUN usermod -u 1000 www-data
