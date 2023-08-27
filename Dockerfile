FROM alpine:3.18.3 as cushon

ENV SERVICEUSER=apache \
    SERVICEGROUP=apache

RUN set -x && \
    apk add --no-cache git apache2 icu-data-full icu-libs su-exec ghostscript && \
    sed -i '/ServerSignature/s/On$/Off/g; /ServerTokens/s/OS$/Prod/g' /etc/apache2/httpd.conf && \
    rm /etc/apache2/conf.d/default.conf /etc/apache2/conf.d/info.conf /etc/apache2/conf.d/userdir.conf && \
    chown -R apache:apache /run/apache2 /var/www /var/log/apache2 && \
    ln -sf /dev/stdout /var/log/apache2/access.log && \
    ln -sf /dev/stderr /var/log/apache2/error.log && \
    apk add --no-cache libcap && setcap 'cap_net_bind_service=+ep' /usr/sbin/httpd && apk del --no-cache libcap
COPY .Docker/root /
EXPOSE 80

RUN apk add --no-cache bash curl openssl runit
COPY .Docker/sbin /sbin/

ENV \
    PHP_INI_DIR=/etc/php81 \
    PHPIZE_DEPS="autoconf file g++ gcc libc-dev make pkgconf re2c"

RUN set -x && \
    apk add curl-dev openssl-dev
RUN set -x && \
    apk add --no-cache \
        imagemagick \
        php81-common \
        php81-bcmath \
        php81-pecl-apcu \
        php81-pdo \
        php81-calendar \
        php81-mbstring \
        php81-exif \
        php81-ftp \
        php81-zip \
        php81-sysvsem \
        php81-sysvshm \
        php81-shmop \
        php81-sockets \
        php81-zlib \
        php81-bz2 \
        php81-curl \
        php81-simplexml \
        php81-xml \
        php81-opcache \
        php81-dom \
        php81-xmlreader \
        php81-xmlwriter \
        php81-tokenizer \
        php81-ctype \
        php81-session \
        php81-fileinfo \
        php81-json \
        php81-posix \
        php81-pear \
        php81-apache2 \
        php81-phar \
        php81-gd \
        php81-gettext \
        php81-gmp \
        php81-pecl-imagick \
        php81-iconv \
        php81-imap \
        php81-intl \
        php81-ldap \
        php81-mysqli \
        php81-mysqlnd \
        php81-pcntl \
        php81-pdo_mysql \
        php81-soap \
        php81-sodium \
        php81-tidy \
        php81-xsl \
        php81-openssl


RUN set -x && \
    php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer && \
    sed -i '/expose_php/ s/On/Off/' "${PHP_INI_DIR}/php.ini"

RUN pecl config-set php_ini "{PHP_INI_DIR}/php.ini"

RUN apk add mysql-client

COPY .Docker/entrypoint.sh /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]