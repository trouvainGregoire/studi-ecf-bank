<?php


namespace App\Service;


use App\Entity\Banker;
use App\Entity\Client;
use App\Entity\Recipient;
use Doctrine\ORM\EntityManagerInterface;

class BankerUtils
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getPendingAccounts(Banker $banker): array
    {
        $clients = $banker->getClients();

        $pendingAccounts = [];

        foreach ($clients as $client) {
            if ($client->getAccount()->getStatus() === 'pending') {
                array_push($pendingAccounts, $client);
            }
        }

        return $pendingAccounts;
    }

    public function validateRecipient(Recipient  $recipient)
    {
        $recipient
            ->setStatus('activated');

        $this->entityManager->persist($recipient);
        $this->entityManager->flush();
    }

    public function getActivatedAccounts(Banker $banker): array
    {
        $clients = $banker->getClients();

        $activatedAccounts = [];

        foreach ($clients as $client) {
            if ($client->getAccount()->getStatus() === 'activated') {
                array_push($activatedAccounts, $client);
            }
        }

        return $activatedAccounts;
    }

    public function getPendingRecipients(Banker $banker): array
    {
        $clients = $banker->getClients();

        $pendingRecipients = [];

        foreach ($clients as $client) {
            foreach ($client->getRecipients() as $recipient){
                if($recipient->getStatus() === 'pending'){
                    array_push($pendingRecipients, $recipient);
                }
            }
        }

        return $pendingRecipients;
    }


    public function validateAccount(Client $client)
    {
        $cleanNumber = preg_replace( '/[^0-9]/', '', microtime(false) );
        $id = base_convert($cleanNumber, 10, 36);

        $account = $client->getAccount();

        $account->setIdentifier('BK' . $id)
            ->setStatus('activated')
            ->setBalance(150);

        $this->entityManager->persist($account);
        $this->entityManager->flush();

    }
}