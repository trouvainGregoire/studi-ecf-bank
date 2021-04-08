<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientDashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="client_dashboard")
     */
    public function index(): Response
    {
        return $this->render('client_dashboard/index.html.twig', [
            'controller_name' => 'ClientDashboardController',
        ]);
    }
}
