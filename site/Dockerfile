ARG PHP_VERSION=7.3

FROM php:${PHP_VERSION}-fpm

ARG PHALCON_VERSION=3.4.4
ARG PHALCON_EXT_PATH=php7/64bits

RUN apt update && apt install -y \
    git \
    curl \
    zip

# Phalcon install
RUN set -xe && \
        cd /tmp && \
        # Compile Phalcon
        curl -LO https://github.com/phalcon/cphalcon/archive/v${PHALCON_VERSION}.tar.gz && \
        tar xzf /tmp/v${PHALCON_VERSION}.tar.gz && \
        docker-php-ext-install -j $(getconf _NPROCESSORS_ONLN) /tmp/cphalcon-${PHALCON_VERSION}/build/${PHALCON_EXT_PATH} && \
        # Remove all temp files
        rm -r \
            /tmp/v${PHALCON_VERSION}.tar.gz \
            /tmp/cphalcon-${PHALCON_VERSION}

# XDebug
RUN pecl install xdebug

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD scripts/start.sh /start.sh

RUN chmod 755 /start.sh

WORKDIR "/var/www/html"
CMD ["/start.sh"]
