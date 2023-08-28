<?php

namespace App\DataFixtures;

use App\Entity\Calendar;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CalendarFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Filesystem $filesystem
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $sourcePath = __DIR__.'/logo.svg';
        $tmpPath = $this->filesystem->tempnam(sys_get_temp_dir(), 'upload');
        $this->filesystem->copy($sourcePath, $tmpPath, true);

        $calendar = new Calendar();
        $calendar
            ->setName('Test calendar')
            ->setSlug('test')
            ->setLogoFile(new UploadedFile(
                $tmpPath,
                basename($sourcePath),
                null,
                null,
                true
            ))
            ->addPerson($this->getReference('person:person-0'))
            ->addPerson($this->getReference('person:person-1'))
            ->addPerson($this->getReference('person:person-2'))
        ;
        $manager->persist($calendar);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            PersonFixture::class,
        ];
    }
}
