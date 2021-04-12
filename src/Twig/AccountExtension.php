<?php

namespace App\Twig;

use App\Entity\Account;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AccountExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
          new TwigFunction('findAccount', [$this, 'findAccount'])
        ];
    }

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAccount(string $identifier)
    {
        $accountRepo = $this->entityManager->getRepository(Account::class);


        return $accountRepo->findOneBy(['identifier' => $identifier]);
    }
}