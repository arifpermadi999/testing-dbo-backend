# Testing Backend DBO

using laravel and docker

## Installation

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

