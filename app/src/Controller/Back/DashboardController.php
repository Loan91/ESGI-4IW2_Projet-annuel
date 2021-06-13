<?php

namespace App\Controller\Back;

use App\ChartBuilder\LineChartByMonthBuilder;
use App\Repository\PropertyRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(
        UserRepository $userRepository,
        PropertyRepository $propertyRepository,
        LineChartByMonthBuilder $lineChartBuilder
    ): Response {

        return $this->render('back/dashboard.html.twig', [
            'userCount' => $userRepository->getTotalCount(),
            'newUsersCount' => $userRepository->getUserCountRegisteredThisMonth(),
            'propertyCount' => $propertyRepository->getTotalCount(),
            'newPropertiesCount' => $propertyRepository->getPropertyCountRegisteredThisMonth(),
            'maisonCount' => $propertyRepository->getMaisonCount(),
            'appartementCount' => $propertyRepository->getAppartementCount(),
            'chart' => $lineChartBuilder->build([
                'data' => $userRepository->getUsersOnYearByMonths('CURRENT_YEAR')
            ])
        ]);
    }
}
