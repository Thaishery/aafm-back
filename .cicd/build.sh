#!/bin/sh
cd /var/www/symfony_docker/aafm-back/
# chmod +x ./bin/console
# Run migrations
php bin/console lexik:jwt:generate-keypair --skip-if-exists
make tests
symfony console doctrine:migrations:migrate --no-interaction
