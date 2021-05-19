#!/bin/bash
printf "chatbot-app setup 🚀\n\n"
printf "clean up\n"
./vendor/bin/sail down -v
rm .env
cp .env.example .env
printf "starting containers and services... 🚀\n"
./vendor/bin/sail up -d

until [[ $(docker ps -q --filter="health=healthy" --filter="name=mysql") ]]; do
  printf "waiting for mysql container to start ...\n"
  sleep 5
done

printf "running migrations... 🚀\n"
./vendor/bin/sail artisan migrate
printf "running db seeds... 🚀\n"
./vendor/bin/sail artisan db:seed

printf "\n\nYou can now access the application on http://localhost\n\n"