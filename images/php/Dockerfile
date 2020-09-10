FROM php:7.4-fpm-alpine

RUN apk add postgresql-dev libxml2-dev curl-dev oniguruma-dev && \
docker-php-ext-install pgsql xml simplexml curl pdo mysqli mbstring json sockets posix bcmath