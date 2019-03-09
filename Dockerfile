FROM php:7.2-alpine

LABEL maintainer="Ryan Durham <ryan@stagerightlabs.com>"

RUN apk add --no-cache autoconf libtool make g++ \
    curl-dev \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    postgresql-dev \
    icu-dev \
    libxml2-dev \
    bzip2-dev \
    git

# Install PHP Extensions
RUN docker-php-ext-install mbstring pdo_pgsql curl json intl gd xml zip bz2 opcache bcmath

# Configure the PHP GD extension
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/

# clean
RUN apk del autoconf libtool make perl
RUN rm -rf /tmp/* /var/cache/apk/*
