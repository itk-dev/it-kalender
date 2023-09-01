# IT Kalender - deployment

See [development](development.md) for details on development.

```sh
docker compose --env-file .env.docker.local --file docker-compose.server.yml pull
docker compose --env-file .env.docker.local --file docker-compose.server.yml up --detach
docker compose --env-file .env.docker.local --file docker-compose.server.yml exec phpfpm composer install
docker compose --env-file .env.docker.local --file docker-compose.server.yml exec phpfpm bin/console doctrine:migrations:migrate --no-interaction

open "http://$(docker compose port nginx 8080)"
```

## Build assets

```sh
docker compose run --rm node yarn install
docker compose run --rm node yarn build
```

## Cron job

Set up a cron job to read all ICS data regularly:

```sh
0 * * * * docker compose --env-file .env.docker.local --file docker-compose.server.yml exec phpfpm bin/console app:read-ics
```

## Automatically refresh calendar view

Add `refresh=«seconds»` to a calendar view URL to fresh the view automatically
every «seconds» seconds, e.g. `http://localhost/test?refresh=900`.
