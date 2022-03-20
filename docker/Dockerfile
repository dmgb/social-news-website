FROM php:8.0.13-fpm-alpine3.13
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
	echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20200930/xdebug.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"` && \
    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

#xdebug.mode=debug
#xdebug.client_host = host.docker.internal
