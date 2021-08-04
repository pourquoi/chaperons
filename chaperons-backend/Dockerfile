FROM php:7.3.9-fpm-stretch

# NODEJS
# RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    build-essential locales acl zip unzip \
    gnupg gnupg1 gnupg2 \
    libmagickwand-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libgmp-dev \
    supervisor \
    yarn \
    nodejs \
    cron \
    vim

RUN apt-get install -y nginx
RUN rm /etc/nginx/sites-enabled/default
COPY .docker/nginx/default.conf /etc/nginx/sites-enabled/default

RUN apt-get install -y mysql-client

RUN rm -rf /var/lib/apt/lists/*
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# CRON
COPY .docker/cron/jobs.txt /tmp/jobs.txt
RUN crontab /tmp/jobs.txt

# SUPERVISOR
COPY .docker/supervisor/nginx.conf /etc/supervisor/conf.d/nginx.conf
COPY .docker/supervisor/php-fpm.conf /etc/supervisor/conf.d/php-fpm.conf
RUN touch /var/run/supervisor.sock
RUN chmod 777 /var/run/supervisor.sock

# PHP
COPY .docker/php/php.ini /usr/local/etc/php/php.ini
COPY .docker/php/php-fpm-pool.conf /usr/local/etc/php-fpm.d/
RUN touch /var/run/php-fpm.sock
RUN chmod 777 /var/run/php-fpm.sock

# RUN
#RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis
#RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h
RUN docker-php-ext-install gmp bcmath pdo pdo_mysql opcache

# COMPOSER
RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer \
	&& chmod +sx /usr/local/bin/composer

COPY . /app

WORKDIR /app

COPY app/config/parameters.yml.dist app/config/parameters.yml

RUN mkdir -p /app/var/logs
RUN mkdir -p /app/var/cache
RUN mkdir -p /app/var/sessions/dev
RUN mkdir -p /app/var/sessions/prod

RUN setfacl -dRm u:www-data:rwX /app/var/logs /app/var/cache /app/var/sessions
RUN setfacl -Rm u:www-data:rwX /app/var/logs /app/var/cache /app/var/sessions

RUN composer install

RUN chmod a+x bin/up.sh

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]


