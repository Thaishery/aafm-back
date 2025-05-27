#!/bin/sh
echo "Démarrage du serveur de webhook sur le port $webhook_port"
ngrok http $webhook_port --domain $NGROK_BACK_URL > /dev/null &
ruby /tmp/.cicd/webhook.rb >/dev/null 2>&1 &
fa