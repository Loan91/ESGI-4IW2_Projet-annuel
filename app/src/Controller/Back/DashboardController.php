<?php

namespace App\Controller\Back;

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
    public function index(UserRepository $userRepository, PropertyRepository $propertyRepository): Response
    {       
        return $this->render('back/dashboard.html.twig', [
            'userCount' => $userRepository->getTotalCount(),
            'newUsersCount' => $userRepository->getUserCountRegisteredThisMonth(),
            'propertyCount' => $propertyRepository->getTotalCount(),
            'newPropertiesCount' => $propertyRepository->getPropertyCountRegisteredThisMonth(),
            'maisonCount' => $propertyRepository->getMaisonCount(),
            'appartementCount' => $propertyRepository->getAppartementCount()
        ]);
    }
}
