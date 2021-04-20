# Chatbot PHP Challenge

## Technologies used
- Laravel 8
    - Sail for docker environment
    - Sanctum for authentication and CSRF protection
    - Dusk for testing using selecnium and chrome-drive
- Redis for cache of currency list and rates
- VueJS for the main interface
- Mocha for javascript unit tests

### Requirements
- Docker

### How to run the application
- Install laravel dependencies
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php80-composer:latest \
    composer install --ignore-platform-reqs
```
- Run the following commands inside chatbot-app folder
```
$ cp .env.example .env
$ ./vendor/bin/sail up -d
$ ./vendor/bin/sail artisan migrate
$ ./vendor/bin/sail artisan db:seed
$ ./vendor/bin/sail npm install
$ ./vendor/bin/sail npm run prod
 ```

- After that you can access the chatbot on http://localhost

### Default user
You can use the chatbot to register a new user if you want, but you can use this user created in the seed process as well
```
email: jack@test.com
password: mygoodpassword123@
```

- To stop the application containers run the following command
 ```
 $ ./vendor/bin/sail down
 ```

## How to use
- These are the words that triggers actions on the chat
```
help, login, log-in, sign-up, signup, register, logout, log-out, deposit, withdraw, balance, funds
```
- When using deposit or withdraw you can specify a currency code

## Automated tests
- To run automate browser tests use dusk (it needs sail up and running)
```
$ ./vendor/bin/sail dusk
```
- To run some javascript unit tests use npm (it needs sail up and running)
```
$ ./vendor/bin/sail npm run test
```
