# IT Kalender

```sh
docker compose pull
docker compose up --detach
docker compose exec phpfpm composer install

open "http://$(docker compose port nginx 8080)"
```

## Build assets

```sh
docker compose run --rm node yarn install
docker compose run --rm node yarn build
```

Watch for changes during development:

```sh
docker compose run --rm node yarn watch
```

## Coding standards

```sh
docker compose exec phpfpm composer coding-standards-check
```

```sh
docker compose exec phpfpm composer coding-standards-apply
```

```sh
docker compose run --rm node yarn coding-standards-check
```

```sh
docker compose run --rm node yarn coding-standards-apply
```
