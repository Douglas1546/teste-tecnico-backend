FROM ubuntu:24.04

WORKDIR /var/www/html/

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get --yes install -qy software-properties-common
RUN apt-get install -qy git curl libmcrypt-dev mariadb-client ghostscript libzip-dev zlib1g-dev zip openssh-client
RUN add-apt-repository ppa:ondrej/php
RUN apt-get -y install php7.0
RUN apt-get --yes --force-yes install php7.0 php7.0-cli php7.0-common php7.0-json php7.0-opcache php7.0-mysql php7.0-mbstring php7.0-mcrypt php7.0-zip php7.0-fpm php7.0-xml php7.0-gd php7.0-curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 8000