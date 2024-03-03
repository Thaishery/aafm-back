#!/bin/sh
cd /var/www/symfony_docker/aafm-back/
# chmod +x ./bin/console
# Run migrations
make tests
symfony console doctrine:migrations:migrate --no-interaction
