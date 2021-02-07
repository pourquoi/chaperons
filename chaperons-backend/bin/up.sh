#!/bin/bash

php bin/console doctrine:schema:create -qn

php bin/console doctrine:database:import -qn src/App/get_total_nurseries.sql
php bin/console doctrine:database:import -qn src/App/update_close_nurseries.sql

php bin/console cache:warmup -qn

mkdir -p /app/var/{logs,cache,session}/dev
chmod -R 777 /app/var/{logs,cache,sessions}/*

chown -R www-data:www-data /app/var/logs
chown -R www-data:www-data /app/var/cache
chown -R www-data:www-data /app/var/sessions