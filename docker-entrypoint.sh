#!/bin/bash

set -e

cd /var/www/symfony_docker
# Run migrations
sleep 10
symfony console doctrine:migrations:migrate --no-interaction
# Start Symfony server
#! dev only ... 
php-fpm -F -R
# symfony server:start --port=9001