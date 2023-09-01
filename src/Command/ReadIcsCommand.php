<?php

namespace App\Command;

use App\ICS\ICSHelper;
use App\Repository\PersonRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:read-ics',
    description: 'Read ICS for all people',
)]
class ReadIcsCommand extends Command
{
    public function __construct(
        private readonly PersonRepository $personRepository,
        private readonly ICSHelper $icsHelper
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new ConsoleLogger($output);
        $people = $this->personRepository->findAll();
        foreach ($people as $person) {
            $logger->info(sprintf('Reading ICS for %s', $person->getName()));
            try {
                $this->icsHelper->readICS($person);
                $logger->info(sprintf('ICS read at %s', $person->getIcsReadAt()->format(\DateTimeImmutable::ATOM)));
            } catch (\Exception $exception) {
                $logger->error(sprintf('Error reading ICS: %s', $exception->getMessage()));
            }
        }

        return Command::SUCCESS;
    }
}
