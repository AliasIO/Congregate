FROM phusion/baseimage

MAINTAINER Elbert Alias <elbert@alias.io>

ENV PROJECT_FOLDER /usr/local/congregate/

ENV DEBIAN_FRONTEND noninteractive

RUN mkdir -p $PROJECT_FOLDER

ADD . $PROJECT_FOLDER

WORKDIR $PROJECT_FOLDER

# Apt
RUN \
	apt-get update && apt-get install -y \
	php5-cli \
	&& apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Composer
RUN \
	curl -sS https://getcomposer.org/installer | php && \
	php composer.phar install

RUN php init.php

ENTRYPOINT ["php", "index.php"]
