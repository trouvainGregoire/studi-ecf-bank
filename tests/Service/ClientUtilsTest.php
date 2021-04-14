<?php


namespace Service;

use App\Entity\Account;
use App\Entity\Banker;
use App\Entity\Client;
use App\Entity\Recipient;
use App\Service\BankerUtils;
use App\Service\ClientUtils;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientUtilsTest extends KernelTestCase
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
        $account->setIdentifier('BKTEST');

        $this->entityManager->persist($account);

        $client->setAccount($account);

        $this->entityManager->persist($client);

        $recipient = new Recipient();

        $recipient
            ->setName('Name-test')
            ->setFirstname('Firstname-test')
            ->setAccountIdentifier($account->getIdentifier());

        $client->addRecipient($recipient);

        $this->entityManager->persist($recipient);

        $this->entityManager->persist($client);

        $this->entityManager->flush();
    }

    public function testSetup()
    {
        $this->createDataForTesting();
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client@bankin.net']);
        $recipient = $this->entityManager->getRepository(Recipient::class)->findOneBy(['name' => 'Name-test']);

        $this->assertNotNull($recipient);
        $this->assertNotNull($client);
    }

    private function getClient(): Client
    {
        return $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client@bankin.net']);
    }

    private function getRecipient(): Recipient
    {
        return $this->entityManager->getRepository(Recipient::class)->findOneBy(['name' => 'Name-test']);
    }

    public function testGetPendingRecipients()
    {
        $this->createDataForTesting();

        $clientUtils = new ClientUtils($this->entityManager);

        $this->assertEquals(1, count($clientUtils->getPendingRecipients($this->getClient())));

    }

    public function testGetActivatedRecipients()
    {
        $this->createDataForTesting();

        $clientUtils = new ClientUtils($this->entityManager);

        $recipient = $this->getRecipient();
        $recipient->setStatus('activated');
        $this->entityManager->persist($recipient);
        $this->entityManager->flush();

        $this->assertEquals(1, count($clientUtils->getActivatedRecipients($this->getClient())));

    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}