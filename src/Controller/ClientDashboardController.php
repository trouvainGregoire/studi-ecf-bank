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

        /** @var \App\Entity\Client $client */
        $client = $this->getUser();

        $isPendingAccount = $client->getAccount()->getStatus() === 'pending';

        return $this->render('client_dashboard/index.html.twig', [
            'message' => $isPendingAccount ? 'Votre compte est en attende de validation' : '',
            'isPending' => $isPendingAccount,
            'client' => $client
        ]);
    }
}
