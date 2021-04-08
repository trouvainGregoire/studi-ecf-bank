<?php


namespace App\Service;


use App\Entity\Account;
use App\Entity\Banker;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class ClientDispatcher
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function dispatch(Client $client): void
    {
        $pendingAccounts = $this->entityManager->getRepository(Account::class)->findBy(['status' => 'pending']);
        $bankers = $this->entityManager->getRepository(Banker::class)->findAll();

        $bankersScore = [];

        $defaultScore = 1;

        foreach ($bankers as $banker){
            $bankersScore[$banker->getId()] = $defaultScore;
        }

        $banker = null;

        foreach ($pendingAccounts as $account){
            $accountClient = $account->getClient();
            $bankerCLient = $accountClient->getBanker();

            if(!$bankerCLient){
                continue;
            }

            if(array_key_exists($bankerCLient->getId(), $bankersScore)){
                $bankersScore[$bankerCLient->getId()] = $bankersScore[$bankerCLient->getId()] + 1;
            }else{
                $bankersScore[$bankerCLient->getId()] = $defaultScore;
            }

        }

        if(!empty($bankersScore)){
            asort($bankersScore, SORT_NUMERIC);
            $banker = $this->entityManager->getRepository(Banker::class)->find(array_key_first($bankersScore));
        }else{
            $banker = $this->entityManager->getRepository(Banker::class)->findAll()[0];
        }

        $banker->addClient($client);

        $this->entityManager->persist($banker);
        $this->entityManager->flush();
    }
}