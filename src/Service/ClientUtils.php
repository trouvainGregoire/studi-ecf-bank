<?php


namespace App\Service;


use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ClientUtils
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getActivatedRecipients(Client $client): array
    {
        $activatedRecipients = [];

        foreach ($client->getRecipients() as $recipient) {
            if ($recipient->getStatus() === 'activated') {
                array_push($activatedRecipients, $recipient);
            }
        }

        return $activatedRecipients;
    }

    public function getPendingRecipients(Client $client): array
    {
        $pendingRecipients = [];

        foreach ($client->getRecipients() as $recipient) {
            if ($recipient->getStatus() === 'pending') {
                array_push($pendingRecipients, $recipient);
            }
        }

        return $pendingRecipients;
    }
}