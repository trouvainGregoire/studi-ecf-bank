<?php


namespace App\Service;


use App\Entity\Banker;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class BankerUtils
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
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