# Testing Backend DBO

using laravel and docker

## API Documentation

```bash
https://documenter.getpostman.com/view/4384145/UVJfkFv4
```


## Installation

install composer in backend-dbo\src

```bash
composer install
```

rename .env.example to .env

```bash
.env.example to .env
```

run docker in backend-dbo

```bash
docker-compose up -d --build
```

then run migration

```bash
docker-compose run --rm php php /testing/dbo/artisan migrate
```

run api on

```bash
http://localhost:8080/api
```

