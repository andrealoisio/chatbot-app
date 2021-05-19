#!/bin/bash
./vendor/bin/sail up -d
until [[ $(docker ps -q --filter="health=healthy" --filter="name=mysql") ]]; do
  printf "waiting for mysql container to start ...\n"
  sleep 5
done
printf "\n\nYou can now access the application on http://localhost\n\n"