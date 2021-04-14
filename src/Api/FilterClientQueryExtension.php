<?php

namespace App\Api;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;


class FilterClientQueryExtension implements QueryCollectionExtensionInterface
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

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if (!$this->security->isGranted('ROLE_BANKER')) {
            return;
        }

        $banker = $this->security->getUser();

        if (Client::class === $resourceClass) {
            $queryBuilder
                ->leftJoin($queryBuilder->getRootAliases()[0] . '.banker', 'banker')
                ->where('banker = :bankerId')
                ->setParameter('bankerId', $banker)
                ->leftJoin($queryBuilder->getRootAliases()[0] . '.account', 'account')
                ->andWhere('account.status = :activeStatus')
                ->setParameter('activeStatus', 'activated')
            ;
        }

    }
}