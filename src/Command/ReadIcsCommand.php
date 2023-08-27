<?php

namespace App\Command;

use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:read-ics',
    description: 'Add a short description for your command',
)]
class ReadIcsCommand extends Command
{
    public function __construct(
        private readonly PersonRepository $personRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $people = $this->personRepository->findAll();
        foreach ($people as $person) {
            try {
                $ics = file_get_contents($person->getIcsUrl());
                $person
                    ->setIcs($ics)
                    ->setIcsReadAt(new \DateTimeImmutable());
                $this->entityManager->persist($person);
                $this->entityManager->flush();
            } catch (\Throwable $t) {
            }
        }

        return Command::SUCCESS;
    }
}
