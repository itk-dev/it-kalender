<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\ICS\ICSHelper;
use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    public function __construct(
        private readonly ICSHelper $icsHelper
    ) {
    }

    #[Route('/', name: 'calendar_index')]
    public function index(CalendarRepository $repository): Response
    {
        $calendars = $repository->findAll();

        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendars,
        ]);
    }

    #[Route('/{slug}', name: 'calendar_show')]
    public function show(Request $request, Calendar $calendar): Response
    {
        $parameters = $this->getData($request, $calendar);
        $parameters['calendar'] = $calendar;

        return $this->render('calendar/show.html.twig', $parameters);
    }

    #[Route('/data/{slug}', name: 'app_data')]
    public function data(Request $request, Calendar $calendar): JsonResponse
    {
        return new JsonResponse($this->getData($request, $calendar));
    }

    private function getData(Request $request, Calendar $calendar): array
    {
        $now = new \DateTimeImmutable('today');
        try {
            $now = new \DateTimeImmutable($request->get('today'));
        } catch (\Throwable) {
        }

        return $this->icsHelper->getCalendarData($calendar, now: $now);
    }
}
