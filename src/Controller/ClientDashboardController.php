<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Recipient;
use App\Form\ClientType;
use App\Form\RecipientType;
use App\Service\ClientUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/dashboard/recipients", name="client_show_recipients")
     */
    public function showActivatedRecipients(): Response
    {
        /** @var \App\Entity\Client $client */
        $client = $this->getUser();

        return $this->render('client_dashboard/show_activated_recipients.html.twig', [
            'recipients' => $client->getRecipients(),
            'isPending' => $client->getAccount()->getStatus() === 'pending'
        ]);
    }

    /**
     * @Route("/dashboard/create-recipient", name="client_create_recipient")
     */
    public function createRecipient(Request $request): Response
    {
        /** @var \App\Entity\Client $client */
        $client = $this->getUser();

        $recipient = new Recipient();

        $form = $this->createForm(RecipientType::class, $recipient);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $recipient->setClient($client);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recipient);
            $entityManager->flush();

            return $this->redirectToRoute('client_show_recipients');
        }

        return $this->render('client_dashboard/create_recipient.html.twig', [
            'recipients' => $client->getRecipients(),
            'isPending' => $client->getAccount()->getStatus() === 'pending',
            'form' => $form->createView(),
        ]);
    }
}
