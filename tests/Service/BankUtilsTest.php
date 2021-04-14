<?php


namespace Service;


use App\Entity\Account;
use App\Entity\Banker;
use App\Entity\Client;
use App\Entity\Recipient;
use App\Entity\Transaction;
use DateTimeImmutable;
use App\Service\BankUtils;
use Doctrine\ORM\EntityManager;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BankUtilsTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function setUp(): void
    {
        $kernel = static::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    private function createDataForTesting()
    {
        $banker = new Banker();
        $email = 'banker@bankin.net';
        $banker->setEmail($email)
            ->setPassword('123456');

        $client = new Client();
        $client->setEmail('client@bankin.net')
            ->setPassword('12345')
            ->setName('Name-test')
            ->setCity('Strasbourg')
            ->setFirstname('First-test')
            ->setBirthdate(new DateTimeImmutable())
            ->setAddress('5 rue du midi')
            ->setZipcode(75015);

        $account = new Account();
        $account->setBalance(150)->setIdentifier('BKTEST');

        $client->setAccount($account);

        $banker->addClient($client);

        $client2 = new Client();
        $client2->setEmail('client2@bankin.net')
            ->setPassword('12345')
            ->setName('Name-test-2')
            ->setFirstname('First-test-2')
            ->setCity('Strasbourg')
            ->setBirthdate(new DateTimeImmutable())
            ->setAddress('5 rue du midi')
            ->setZipcode(75015);

        $account2 = new Account();
        $account2->setBalance(150)->setIdentifier('BKTEST2');

        $client2->setAccount($account2);

        $recipient = new Recipient();
        $recipient
            ->setFirstname('First-test')
            ->setName('Name-test')
            ->setAccountIdentifier('BKTEST2');

        $this->entityManager->persist($recipient);

        $client->addRecipient($recipient);

        $this->entityManager->persist($client);

        $this->entityManager->persist($banker);

        $this->entityManager->persist($client2);


        $this->entityManager->flush();
    }

    public function testMakeTransaction()
    {
        $this->createDataForTesting();

        $bankUtils = new BankUtils($this->entityManager);

        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client@bankin.net']);

        $recipient = $client->getRecipients()[0];

        $transaction = new Transaction();

        $transaction
            ->setAmount(75.89)
            ->setDescription('test');

        $bankUtils->makeTransaction($client, $transaction, $recipient);

        $clientAccount = $client->getAccount();
        $client2Account = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client2@bankin.net'])->getAccount();

        $this->assertEquals(74.11, $clientAccount->getBalance());
        $this->assertEquals(225.89, $client2Account->getBalance());

        $client1Transaction = $this->entityManager->getRepository(Transaction::class)->findOneBy(['account' => $clientAccount->getId()]);
        $client2Transaction = $this->entityManager->getRepository(Transaction::class)->findOneBy(['account' => $client2Account->getId()]);

        $this->assertNotNull($client1Transaction);
        $this->assertNotNull($client2Transaction);

        $this->assertEquals('credit', $client2Transaction->getType());
        $this->assertEquals(75.89, $client2Transaction->getAmount());
        $this->assertEquals('debit', $client1Transaction->getType());
        $this->assertEquals(75.89, $client1Transaction->getAmount());

    }

    public function testMakeInvalidTransaction()
    {
        $this->createDataForTesting();

        $bankUtils = new BankUtils($this->entityManager);

        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client@bankin.net']);

        $recipient = $client->getRecipients()[0];

        $transaction = new Transaction();

        $transaction
            ->setAmount(175.89)
            ->setDescription('test');

        $this->expectException(LogicException::class);

        $bankUtils->makeTransaction($client, $transaction, $recipient);
    }

    public function testSetPendingRemovalAccount()
    {
        $this->createDataForTesting();

        $bankUtils = new BankUtils($this->entityManager);

        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client@bankin.net']);

        $bankUtils->setPendingRemovalAccount($client->getAccount());

        $this->assertEquals('pending-removal', $client->getAccount()->getStatus());
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}