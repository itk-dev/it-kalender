<?php

namespace App\Command;

use App\CacheHelper;
use App\ICS\ICSHelper;
use App\Repository\CalendarRepository;
use App\Repository\PersonRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:read-ics',
    description: 'Read ICS for all people',
)]
class ReadIcsCommand extends Command
{
    public function __construct(
        private readonly PersonRepository $personRepository,
        private readonly CalendarRepository $calendarRepository,
        private readonly ICSHelper $icsHelper,
        private readonly UrlGeneratorInterface $router,
        private readonly CacheHelper $cacheHelper,
        private readonly HttpClientInterface $httpClient
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('refresh-calendars', null, InputOption::VALUE_NONE, 'Refresh calendars');
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

        if ($input->getOption('refresh-calendars')) {
            foreach ($this->calendarRepository->findAll() as $calendar) {
                try {
                    $logger->info(sprintf('Purging cache for calendar %s', $calendar->getName()));
                    $this->cacheHelper->purgeCalendarCache($calendar, true);
                    $logger->info(sprintf('Rebuilding cache for calendar %s', $calendar->getName()));
                    $start = microtime(true);
                    $url = $this->router->generate('calendar_show', ['slug' => $calendar->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
                    $this->httpClient->request('GET', $url)->getContent();
                    $end = microtime(true);
                    $logger->debug(sprintf('Elapsed time: %d ms', $end - $start));
                } catch (\Exception) {
                    $logger->error(sprintf('Error refreshing calendar %s', $calendar->getName()));
                }
            }
        }

        return Command::SUCCESS;
    }
}
