# IT Kalender

Shows when people are “Out of office” or “Working elsewhere”

* Only appointments lasting more than 2 hours are shown
* An appointment ending on or before 12:00 is show as starting at 08:00 and
  ending at 12:00
* An appointment starting on or after 12:00 is show as starting at 12:00 and
  ending at 16:00
* An appointment starting before 12:00 and ending after 12:00 is shown as at
  08:00 and ending at 16:00, i.e. as a full day

Calendar data is read using ICS feed URLs from Outlook (see [Getting ICS
URL](docs/UserGuide.md#getting-ics-url) in [the user guide](docs/UserGuide.md))
for details on how to get these URLs).

--------------------------------------------------------------------------------

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
docker compose run --rm node yarn build
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

## Cron job

Set up a cron job to read all ICS data regularly:

```sh
0 * * * * docker compose exec phpfpm bin/console app:read-ics
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
