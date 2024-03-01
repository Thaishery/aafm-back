#!/bin/sh

webhook_port=8089

project_dir="/usr/aafm-back"

#création des dossier ssh et copie de la clef(a gérer et ajouter en amont au compte git) (pour git auth):
echo "making : $HOME_DIR"
mkdir $HOME_DIR
# mkdir ~/.ssh/
cp -r /tmp/.ssh/ $HOME_DIR/.ssh/
chmod -R 700 $HOME_DIR

# Setup Git Config
# echo "Setting Git Config Values"
# git config --global user.email "gdeb@gdeb.fr" && \
# git config --global user.name "Thaishery"

# Setup Git Folders
echo "Adding Host Key for Github"
cd $HOME_DIR \
  && touch ~/.ssh/known_hosts \
  && ssh-keyscan -t rsa github.com >> ~/.ssh/known_hosts \

# Add ssh-key to SSH Agent
echo "Adding SSH Key to ssh-agent" \
  && eval `ssh-agent -s` && ssh-add $HOME_DIR/.ssh/id_rsa

touch ~/.ssh/config \
  && echo "Host $HOST_INFO" >> ~/.ssh/config \
  && echo "   ForwardAgent yes" >> ~/.ssh/config

cd /usr

git clone git@github.com:Thaishery/aafm-back.git
cd /usr/aafm-back
pwd
git checkout $ENVIRONMENT

cp -r /usr/aafm-back/.cicd/ /tmp/.cicd/
chmod -R 777 /tmp/.cicd/
# chmod -R +x /usr/aafm-back
# ln -s /usr/aafm-back /var/www/symfony_docker
cp -r /usr/aafm-back/ /var/www/symfony_docker/aafm-back/

build(){
  . /tmp/.cicd/build.sh
}

listen(){
  . /tmp/.cicd/listen.sh
}

# sleep 10
build
listen

# Start Symfony server
php-fpm -F -R