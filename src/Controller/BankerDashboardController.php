<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Banker;
use App\Entity\Client;
use App\Entity\Recipient;
use App\Service\BankerUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BankerDashboardController extends AbstractController
{
    /**
     * @Route("/banker/dashboard", name="banker_dashboard")
     */
    public function index(BankerUtils $bankerUtils): Response
    {
        /** @var Banker $banker */
        $banker = $this->getUser();

        $clients = $banker->getClients();

        return $this->render('banker_dashboard/index.html.twig', [
            'clients' => $clients,
            'pendingAccounts' => $bankerUtils->getPendingAccounts($banker),
            'activatedClients' => $bankerUtils->getActivatedAccounts($banker),
            'pendingRecipients' => $bankerUtils->getPendingRecipients($banker),
            'pendingRemovalAccounts' => $bankerUtils->getPendingRemovalAccounts($banker)
        ]);
    }

    /**
     * @Route("/banker/pending-accounts", name="banker_pending_accounts")
     */
    public function showPendingAccounts(Request $request, BankerUtils $bankerUtils): Response
    {
        /** @var Banker $banker */
        $banker = $this->getUser();

        $formCollection = [];

        foreach ($bankerUtils->getPendingAccounts($banker) as $client) {
            $form = $this->createFormBuilder()
                ->add('clientId', HiddenType::class)
                ->add('save', SubmitType::class, ['label' => 'Valider'])
                ->getForm();
            array_push($formCollection, $form);
        }

        foreach ($formCollection as $form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $clientId = $form->get('clientId')->getData();
                $client = $this->getDoctrine()->getRepository(Client::class)->find($clientId);

                if (!$client) {
                    throw $this->createNotFoundException('No client found for id' . $clientId);
                }

                $bankerUtils->validateAccount($client);
            }
        }

        $formViewCollection = [];

        foreach ($formCollection as $form) {
            array_push($formViewCollection, $form->createView());
        }

        return $this->render('banker_dashboard/show_pending_accounts.html.twig', [
            'pendingAccounts' => $bankerUtils->getPendingAccounts($banker),
            'forms' => $formViewCollection
        ]);
    }

    /**
     * @Route("/banker/pending-recipients", name="banker_pending_recipients")
     */
    public function showPendingRecipients(Request $request, BankerUtils $bankerUtils): Response
    {
        /** @var Banker $banker */
        $banker = $this->getUser();

        $formCollection = [];

        foreach ($bankerUtils->getPendingRecipients($banker) as $recipient) {
            $form = $this->createFormBuilder()
                ->add('recipientId', HiddenType::class)
                ->add('save', SubmitType::class, ['label' => 'Valider'])
                ->getForm();
            array_push($formCollection, $form);
        }

        foreach ($formCollection as $form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $recipientId = $form->get('recipientId')->getData();
                $recipient = $this->getDoctrine()->getRepository(Recipient::class)->find($recipientId);

                if (!$recipient) {
                    throw $this->createNotFoundException('No recipient found for id' . $recipientId);
                }

                $bankerUtils->validateRecipient($recipient);
            }
        }

        $formViewCollection = [];

        foreach ($formCollection as $form) {
            array_push($formViewCollection, $form->createView());
        }

        return $this->render('banker_dashboard/show_pending_recipients.html.twig', [
            'pendingRecipients' => $bankerUtils->getPendingRecipients($banker),
            'forms' => $formViewCollection
        ]);
    }

    /**
     * @Route("/banker/pending-removal-accounts", name="banker_pending_removal_accounts")
     */
    public function showPendingRemovalAccount(Request $request, BankerUtils $bankerUtils): Response
    {
        /** @var Banker $banker */
        $banker = $this->getUser();

        $formCollection = [];

        foreach ($bankerUtils->getPendingRemovalAccounts($banker) as $pendingRemovalAccount) {
            $form = $this->createFormBuilder()
                ->add('accountId', HiddenType::class)
                ->add('save', SubmitType::class, ['label' => 'Supprimer'])
                ->getForm();
            array_push($formCollection, $form);
        }

        foreach ($formCollection as $form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $accountId = $form->get('accountId')->getData();
                $account = $this->getDoctrine()->getRepository(Account::class)->find($accountId);

                if (!$account) {
                    throw $this->createNotFoundException('No account found for id' . $accountId);
                }

                $bankerUtils->deleteAccount($account);
            }
        }

        $formViewCollection = [];

        foreach ($formCollection as $form) {
            array_push($formViewCollection, $form->createView());
        }

        return $this->render('banker_dashboard/show_pending_removal_accounts.html.twig', [
            'pendingRemovalAccounts' => $bankerUtils->getPendingRemovalAccounts($banker),
            'forms' => $formViewCollection
        ]);
    }
}
