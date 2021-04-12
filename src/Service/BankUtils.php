<?php


namespace App\Service;


use App\Entity\Account;
use App\Entity\Client;
use App\Entity\Recipient;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;

class BankUtils
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function makeTransaction(Client $client, Transaction $transaction, Recipient $recipient)
    {
        if ($client->getAccount()->getBalance() < $transaction->getAmount()) {
            throw new LogicException('Le montant de la transaction doit être inférieur ou égale au solde du client.');
        }

        $recipientAccount = $this->entityManager->getRepository(Account::class)->findOneBy(['identifier' => $recipient->getAccountIdentifier()]);

        if (!$recipientAccount) {
            throw new LogicException('Le compte du bénéficiaire doit être valide.');
        }

        $transaction->setAccount($recipientAccount);

        $this->entityManager->persist($transaction);

        $oldRecipientBalance = $recipientAccount->getBalance();
        $newRecipientBalance = $oldRecipientBalance + $transaction->getAmount();

        $recipientAccount->setBalance($newRecipientBalance);

        $this->entityManager->persist($recipientAccount);

        $clientTransaction = new Transaction();
        $clientTransaction
            ->setType('debit')
            ->setAmount($transaction->getAmount())
            ->setAccount($client->getAccount())
            ->setDescription($transaction->getDescription());

        $this->entityManager->persist($clientTransaction);

        $clientAccount = $client->getAccount();
        $oldClientBalance = $clientAccount->getBalance();
        $newClientBalance = $oldClientBalance - $clientTransaction->getAmount();
        $clientAccount->setBalance($newClientBalance);

        $this->entityManager->persist($clientAccount);

        $this->entityManager->flush();
    }
}