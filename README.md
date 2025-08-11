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