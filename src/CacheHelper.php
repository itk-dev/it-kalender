<?php

namespace App;

use App\Entity\Calendar;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CacheHelper
{
    public function __construct(
        private readonly StoreInterface $store,
        private UrlGeneratorInterface $router
    ) {
    }

    public function purgeCalendarCache(Calendar $calendar, bool $includeIndex = false): void
    {
        $url = $this->router->generate('calendar_show', ['slug' => $calendar->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->store->purge($url);

        if ($includeIndex) {
            $url = $this->router->generate('calendar_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $this->store->purge($url);
        }
    }
}
