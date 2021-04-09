<?php

namespace App\Controller;

use App\Entity\Client;
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
        /** @var \App\Entity\Banker $banker */
        $banker = $this->getUser();

        $clients = $banker->getClients();

        return $this->render('banker_dashboard/index.html.twig', [
            'clients' => $clients,
            'pendingAccounts' => $bankerUtils->getPendingAccounts($banker),
            'activatedClients' => $bankerUtils->getActivatedAccounts($banker)
        ]);
    }

    /**
     * @Route("/banker/pending-accounts", name="banker_pending_accounts")
     */
    public function showPendingAccounts(Request $request, BankerUtils $bankerUtils): Response
    {
        /** @var \App\Entity\Banker $banker */
        $banker = $this->getUser();

        $formCollection = [];

        foreach ($bankerUtils->getPendingAccounts($banker) as $client){
            $form = $this->createFormBuilder()
                ->add('clientId', HiddenType::class)
                ->add('save', SubmitType::class, ['label' => 'Valider'])
                ->getForm();
            array_push($formCollection, $form);
        }

        foreach ($formCollection as $form){
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $clientId = $form->get('clientId')->getData();
                $client = $this->getDoctrine()->getRepository(Client::class)->find($clientId);

                if(!$client){
                    throw $this->createNotFoundException('No client found for id' . $clientId);
                }

                $bankerUtils->validateAccount($client);
            }
        }

        $formViewCollection = [];

        foreach ($formCollection as $form){
            array_push($formViewCollection, $form->createView());
        }

        return $this->render('banker_dashboard/show_pending_accounts.html.twig', [
            'pendingAccounts' => $bankerUtils->getPendingAccounts($banker),
            'forms' => $formViewCollection
        ]);
    }
}
