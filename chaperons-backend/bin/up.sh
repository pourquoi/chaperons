#!/bin/bash

php bin/console doctrine:schema:create -qn || true
php bin/console doctrine:schema:update -qn --force

mysql -h maria -u cartochaperons -pcartochaperons cartochaperons < src/App/update_close_nurseries.sql
mysql -h maria -u cartochaperons -pcartochaperons cartochaperons < src/App/get_total_nurseries.sql

php bin/console cache:warmup -qn

mkdir -p /app/var/{logs,cache,sessions}/dev
chmod -R 777 /app/var/{logs,cache,sessions}/*

chown -R www-data:www-data /app/var/logs
chown -R www-data:www-data /app/var/cache
chown -R www-data:www-data /app/var/sessions