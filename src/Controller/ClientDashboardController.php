<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Recipient;
use App\Entity\Transaction;
use App\Form\DeleteAccountType;
use App\Form\RecipientType;
use App\Form\TransactionType;
use App\Service\BankUtils;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
        /** @var Client $client */
        $client = $this->getUser();

        $isPendingAccount = $client->getAccount()->getStatus() === 'pending';

        $message = '';

        if ($isPendingAccount) {
            $message = 'Votre compte est en attende de validation';
        } elseif ($client->getAccount()->getStatus() === 'pending-removal') {
            $message = 'Votre compte est en attende de suppression';
        }

        return $this->render('client_dashboard/index.html.twig', [
            'message' => $message,
            'isPending' => $isPendingAccount,
            'client' => $client
        ]);
    }

    /**
     * @Route("/dashboard/recipients", name="client_show_recipients")
     */
    public function showActivatedRecipients(): Response
    {
        /** @var Client $client */
        $client = $this->getUser();

        return $this->render('client_dashboard/show_activated_recipients.html.twig', [
            'recipients' => $client->getRecipients(),
            'isPending' => $client->getAccount()->getStatus() === 'pending'
        ]);
    }

    /**
     * @Route("/dashboard/create-transaction", name="client_create_transaction")
     */
    public function createTransaction(Request $request, BankUtils $bankUtils): Response
    {
        /** @var Client $client */
        $client = $this->getUser();

        $transaction = new Transaction();

        $form = $this->createForm(TransactionType::class, $transaction);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipient = $form->get('recipient')->getData();

            try {
                $bankUtils->makeTransaction($client, $transaction, $recipient);
            } catch (LogicException $exception) {
                $form->addError(new FormError($exception->getMessage()));
                return $this->render('client_dashboard/create_transaction.html.twig', [
                    'recipients' => $client->getRecipients(),
                    'isPending' => $client->getAccount()->getStatus() === 'pending',
                    'form' => $form->createView()
                ]);
            }

            return $this->redirectToRoute('client_dashboard');
        }

        return $this->render('client_dashboard/create_transaction.html.twig', [
            'recipients' => $client->getRecipients(),
            'isPending' => $client->getAccount()->getStatus() === 'pending',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dashboard/create-recipient", name="client_create_recipient")
     */
    public function createRecipient(Request $request): Response
    {
        /** @var Client $client */
        $client = $this->getUser();

        $recipient = new Recipient();

        $form = $this->createForm(RecipientType::class, $recipient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    /**
     * @Route("/dashboard/delete-account", name="client_delete_account")
     */
    public function deleteAccount(Request $request, BankUtils $bankUtils): Response
    {
        /** @var Client $client */
        $client = $this->getUser();

        $account = $client->getAccount();

        $form = $this->createForm(DeleteAccountType::class, $account);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $bankUtils->setPendingRemovalAccount($account);

            return $this->redirectToRoute('client_dashboard');
        }

        return $this->render('client_dashboard/delete_account.html.twig', [
            'recipients' => $client->getRecipients(),
            'isPending' => $client->getAccount()->getStatus() === 'pending',
            'form' => $form->createView(),
        ]);
    }

}
