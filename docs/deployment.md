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

## Calender view parameters

The calendar view accepts a number of query string parameters:

| Name    | Type | Description                          | Example     |
|---------|------|--------------------------------------|-------------|
| days    | int  | Number of days to show               | days=3      |
| refresh | int  | Refresh view every number of seconds | refresh=900 |
