<?php

namespace App\Controller;

use App\ICS\BusyStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(): Response
    {
        $parameters = json_decode($this->data()->getContent(), true);

        return $this->render('default/index.html.twig', $parameters);
    }

    #[Route('/data', name: 'app_data')]
    public function data(): JsonResponse
    {
        $dates = array_map(
            static fn ($index) => (new \DateTimeImmutable(sprintf('2023-05-22 + %d days', $index)))->format(\DateTimeImmutable::ATOM),
            range(0, 4),
        );

        return new JsonResponse([
          'title' => 'ITK Dev',
          'logo_url' => 'logo.svg',
          'dates' => $dates,
          'people' => [
            '1. Kollega' => [
              $dates[0] => [
                [
                  'type' => BusyStatus::WorkingElsewhere->value,
                  'start' => '08:00',
                  'end' => '16:00',
                ],
              ],
            ],

            '2. Kollega' => [
              $dates[2] => [
                [
                  'type' => BusyStatus::WorkingElsewhere->value,
                  'start' => '08:00',
                  'end' => '16:00',
                ],
              ],
              $dates[4] => [
                [
                  'type' => BusyStatus::OutOfOffice->value,
                  'start' => '08:00',
                  'end' => '16:00',
                  'title' => 'Afspadsering',
                ],
              ],
            ],

            '3. Kollega' => [
              $dates[1] => [
                [
                  'type' => BusyStatus::WorkingElsewhere->value,
                  'start' => '08:00',
                  'end' => '16:00',
                ],
              ],
              $dates[4] => [
                [
                  'type' => BusyStatus::WorkingElsewhere->value,
                  'start' => '08:00',
                  'end' => '16:00',
                ],
              ],
            ],

            '4. Kollega' => [
              $dates[2] => [
                [
                  'type' => BusyStatus::WorkingElsewhere->value,
                  'start' => '08:00',
                  'end' => '16:00',
                ],
              ],
              $dates[3] => [
                [
                  'type' => BusyStatus::OutOfOffice->value,
                  'start' => '08:00',
                  'end' => '12:00',
                  'title' => 'Tandlæge (10–12)',
                ],
                [
                  'type' => BusyStatus::WorkingElsewhere->value,
                  'start' => '12:00',
                  'end' => '16:00',
                ],
              ],
              $dates[4] => [
                [
                  'type' => BusyStatus::OutOfOffice->value,
                  'start' => '08:00',
                  'end' => '16:00',
                ],
              ],
            ],

            'n. Kollega' => [
              $dates[2] => [
                [
                  'type' => BusyStatus::WorkingElsewhere->value,
                  'start' => '12:00',
                  'end' => '16:00',
                ],
              ],
            ],
          ],
        ]);
    }
}
