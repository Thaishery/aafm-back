#!/bin/sh
echo "Démarrage du serveur de webhook sur le port $webhook_port"
echo "$NGROK_BACK_URL ${NGROK_BACK_URL}"
ngrok http $webhook_port --domain $NGROK_BACK_URL > /dev/null &
ruby /tmp/.cicd/webhook.rb > /dev/null &