#!/bin/bash

set -e

cd /var/www/symfony_docker
# Run migrations
sleep 5
symfony console doctrine:migrations:migrate --no-interaction
# Start Symfony server
symfony server:start --port=9000 