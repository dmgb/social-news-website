FROM php:8.1.4-fpm-alpine3.15
WORKDIR /var/www	

RUN apk update  --no-cache && \
	apk add php8-dev \
	build-base \
    sudo \
    openssh \
    git \
    npm && \
	docker-php-ext-install pdo_mysql && \
	pecl install xdebug && \
	cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini && \
    LOADED_CONFIGURATION_FILE=`php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"` && \
	echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20210902/xdebug.so" >> $LOADED_CONFIGURATION_FILE && \
    echo "xdebug.mode=debug" >> $LOADED_CONFIGURATION_FILE && \
    echo "xdebug.client_host = host.docker.internal" >> $LOADED_CONFIGURATION_FILE && \
    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
