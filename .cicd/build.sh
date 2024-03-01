#!/bin/sh
cd /usr/aafm-back
pwd
ls -al
# chmod +x ./bin/console
# Run migrations
symfony console doctrine:migrations:migrate --no-interaction
