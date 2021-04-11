<?php


namespace Service;


use App\Entity\Account;
use App\Entity\Banker;
use App\Entity\Client;
use App\Entity\Recipient;
use App\Service\BankerUtils;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BankerUtilsTest extends KernelTestCase
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

    public function testSetup()
    {
        $this->createDataForTesting();
        $banker = $this->entityManager->getRepository(Banker::class)->findOneBy(['email' => 'banker@bankin.net']);
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client@bankin.net']);

        $this->assertNotNull($banker);
        $this->assertNotNull($client);
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
            ->setFirstname('First-test')
            ->setBirthdate(new DateTimeImmutable())
            ->setAddress('5 rue du midi')
            ->setZipcode(75015);

        $account = new Account();

        $client->setAccount($account);

        $banker->addClient($client);

        $this->entityManager->persist($client);

        $this->entityManager->persist($banker);

        $this->entityManager->flush();
    }

    public function testPendingAccounts()
    {
        $this->createDataForTesting();

        $clientAndBanker = $this->getBankerAndClient();

        $bankerUtils = new BankerUtils($this->entityManager);

        $this->assertEquals(1, count($bankerUtils->getPendingAccounts($clientAndBanker['banker'])));
    }

    private function getBankerAndClient(): array
    {
        $banker = $this->entityManager->getRepository(Banker::class)->findOneBy(['email' => 'banker@bankin.net']);
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client@bankin.net']);

        return [
            'client' => $client,
            'banker' => $banker
        ];
    }

    public function testGetActivatedAccounts()
    {
        $this->createDataForTesting();

        $clientAndBanker = $this->getBankerAndClient();

        $bankerUtils = new BankerUtils($this->entityManager);

        $this->assertEquals(0, count($bankerUtils->getActivatedAccounts($clientAndBanker['banker'])));
    }

    public function testValidateAccount()
    {
        $this->createDataForTesting();

        $clientAndBanker = $this->getBankerAndClient();

        $bankerUtils = new BankerUtils($this->entityManager);

        $bankerUtils->validateAccount($clientAndBanker['client']);

        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client@bankin.net']);

        $this->assertNotNull($client->getAccount()->getIdentifier());

        $this->assertStringContainsString('BK', $client->getAccount()->getIdentifier());

        $this->assertEquals('activated', $client->getAccount()->getStatus());

        $this->assertEquals(150, $client->getAccount()->getBalance());
    }

    public function testGetPendingRecipients()
    {
        $this->createDataForTesting();

        $this->generatePendingRecipient();

        $clientAndBanker = $this->getBankerAndClient();

        $bankerUtils = new BankerUtils($this->entityManager);

        $this->assertEquals(1, count($bankerUtils->getPendingRecipients($clientAndBanker['banker'])));
    }

    private function generatePendingRecipient()
    {
        $clientAndBanker = $this->getBankerAndClient();

        $client = $clientAndBanker['client'];

        $bankerUtils = new BankerUtils($this->entityManager);

        $bankerUtils->validateAccount($client);

        $activatedClient = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client@bankin.net']);

        $recipient = new Recipient();

        $recipient
            ->setName('Name-test')
            ->setFirstname('Firstname-test')
            ->setAccountIdentifier($activatedClient->getAccount()->getIdentifier());

        $activatedClient->addRecipient($recipient);

        $this->entityManager->persist($recipient);
        $this->entityManager->persist($activatedClient);
        $this->entityManager->flush();
    }

    public function testValidateRecipient()
    {
        $this->createDataForTesting();

        $this->generatePendingRecipient();

        $clientAndBanker = $this->getBankerAndClient();

        $client = $clientAndBanker['client'];

        $bankerUtils = new BankerUtils($this->entityManager);

        $bankerUtils->validateRecipient($client->getRecipients()[0]);

        $this->assertEquals(0, count($bankerUtils->getPendingRecipients($clientAndBanker['banker'])));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}