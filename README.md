## Instructions

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

- To stop the application containers run the following command
 ```
 $ ./vendor/bin/sail down
 ```

## How to use
- There are the words that triggers actions on the chat
```
help, login, log-in, sign-up, signup, register, logout, log-out, deposit, withdraw, balance, funds
```
- When using deposit or withdraw you can specify a currency code
