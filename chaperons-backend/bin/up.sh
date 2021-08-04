#!/bin/bash

setfacl -dRm u:www-data:rwX /app/var/logs /app/var/cache /app/var/sessions
setfacl -Rm u:www-data:rwX /app/var/logs /app/var/cache /app/var/sessions

php bin/console doctrine:schema:update -qn --force

mysql -h maria -u cartochaperons -pcartochaperons cartochaperons < src/App/update_close_nurseries.sql
mysql -h maria -u cartochaperons -pcartochaperons cartochaperons < src/App/get_total_nurseries.sql

php bin/console cache:warmup -qn