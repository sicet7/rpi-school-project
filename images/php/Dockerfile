FROM php:7.4-fpm-alpine

RUN apk add --update --no-cache postgresql-dev &&\
    apk add --update --no-cache --virtual buildDeps \
            libxml2-dev \
            curl \
            tzdata \
            curl-dev \
            oniguruma-dev && \
            docker-php-ext-install \
            pgsql \
            xml \
            simplexml \
            curl \
            pdo \
            pdo_pgsql \
            mysqli \
            mbstring \
            json \
            sockets \
            posix \
            bcmath && \
            cp /usr/share/zoneinfo/Etc/UTC /etc/localtime && \
            echo 'Etc/UTC' > /etc/timezone && \
            echo "[Date]\ndate.timezone=Etc/UTC" > /usr/local/etc/php/conf.d/timezone.ini && \
            apk del buildDeps