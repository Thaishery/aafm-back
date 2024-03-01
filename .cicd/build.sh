#!/bin/sh
cd /usr/aafm-back
# chmod +x ./bin/console
# Run migrations
symfony console doctrine:migrations:migrate --no-interaction
