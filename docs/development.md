# IT Kalender - development

See [deployment](deployment.md) for details on deployment.

```sh
docker compose pull
docker compose up --detach
docker compose exec phpfpm composer install
docker compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction

open "http://$(docker compose port nginx 8080)"
```

## Fixtures

```sh
# Load fixtures
docker compose exec phpfpm composer fixtures:load
# Show the test calendar
open "http://$(docker compose port nginx 8080)/test?today=2001-01-01"
# Show the test calendar data
open "http://$(docker compose port nginx 8080)/data/test?today=2001-01-01"
```

## Build assets

```sh
docker compose run --rm node yarn install
docker compose run --rm node yarn dev
```

Watch for changes during development:

```sh
docker compose run --rm node yarn watch
```

## Translations

```sh
docker compose exec phpfpm composer update-translations
# Open Danish translations in Poedit (https://poedit.net/)
# Run `brew install poedit` to install Poedit.
open translations/messages+intl-icu.da.xlf
```

## Tests

```sh
docker compose exec phpfpm php bin/phpunit
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
