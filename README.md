# Loan Engine

REST API based app to create, approve, disburse loans


## Documentation


Install Dependencies: Run composer install on your host machine to generate the vendor directory and composer.lock file.

```bash
composer install
```

Start Docker: From the root directory (loan-engine-api/), run:

```bash
docker compose up -d --build
```

This will build the PHP image, start all containers, and initialize the database with the schema.

To run unit test, simply run this command
``` bash
composer test
```

To run the REST app
```
docker compose up -d --build
```

## Viewing Logs
1. Live Tailing (Most Useful for Debugging)
To see a live, real-time stream of logs as they happen, use -f flag.

```
docker-compose logs -f php
```
After running this command, go to Postman and trigger the API call that executes the error_log() function. You will see the message appear instantly in your terminal.

2. Viewing All Past Logs
To see the entire log history since the container was last started, just run the command without the -f flag.

```
docker-compose logs php
```
