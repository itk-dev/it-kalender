<?php

namespace App\EventListener;

use App\CacheHelper;
use App\Entity\Calendar;
use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
class CalendarListener
{
    public function __construct(
        private readonly CacheHelper $cacheHelper
    ) {
    }

    public function postPersist(PostPersistEventArgs $event): void
    {
        $this->handle($event);
    }

    public function postUpdate(PostUpdateEventArgs $event): void
    {
        $this->handle($event);
    }

    private function handle(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if ($object instanceof Calendar) {
            $this->cacheHelper->purgeCalendarCache($object, includeIndex: true);
        } elseif ($object instanceof Person) {
            foreach ($object->getCalendars() as $calendar) {
                $this->cacheHelper->purgeCalendarCache($calendar);
            }
        }
    }
}
