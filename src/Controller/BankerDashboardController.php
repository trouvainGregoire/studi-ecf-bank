<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BankerDashboardController extends AbstractController
{
    /**
     * @Route("/banker/dashboard", name="banker_dashboard")
     */
    public function index(): Response
    {
        return $this->render('banker_dashboard/index.html.twig', [
            'controller_name' => 'BankerDashboardController',
        ]);
    }
}
