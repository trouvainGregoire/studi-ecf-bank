<?php

namespace App\Tests\Service;

use App\Entity\Account;
use App\Entity\Banker;
use App\Service\ClientDispatcher;
use Doctrine\ORM\EntityManager;
use App\Entity\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientDispatcherTest extends KernelTestCase
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

    private function createBankers(){
        for ($i = 0; $i <= 5; $i++) {
            $banker = new Banker();
            $email = 'banker-' . $i . '@bankin.net';
            $banker->setEmail($email)
                ->setPassword('123456');
            $this->entityManager->persist($banker);
        }

        $this->entityManager->flush();
    }

    public function testDispatchWithClients()
    {
        $this->createBankers();

        $bankers = $this->entityManager->getRepository(Banker::class)->findAll();

        for ($i = 0; $i <= 6; $i++) {
            $client = new Client();
            $email = 'client-' . $i . '@bankin.net';
            $client->setEmail($email)
                ->setPassword('12345')
                ->setName('Name-' . $i)
                ->setFirstname('First-' . $i)
                ->setBirthdate(new \DateTimeImmutable())
                ->setAddress('5 rue du midi')
                ->setZipcode(75015);

            $account = new Account();
            $account->setStatus('pending');
            $client->setAccount($account);

            if($i != 6){
                $bankers[$i]->addClient($client);
            }else{
                $bankers[0]->addClient($client);
            }

            $this->entityManager->persist($client);
        }

        $this->entityManager->flush();

        $clientDispatcher = new ClientDispatcher($this->entityManager);

        $testClient = new Client();
        $testClient->setEmail('test@client.fr')
            ->setPassword('12345')
            ->setName('Name-test')
            ->setFirstname('First-test')
            ->setBirthdate(new \DateTimeImmutable())
            ->setAddress('5 rue du midi')
            ->setZipcode(75015);
        $account = new Account();
        $account->setStatus('pending');
        $testClient->setAccount($account);

        $this->entityManager->persist($testClient);
        $this->entityManager->flush();


        $clientDispatcher->dispatch($testClient);

        $resultUser = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'test@client.fr']);
        $resultBanker = $resultUser->getBanker();

        $this->assertNotEquals('banker-0@bankin.net', $resultBanker->getEmail());

    }

    public function testDispatchWithoutClients(){
        $this->createBankers();

        $client = new Client();
        $email = 'client-test@bankin.net';
        $client->setEmail($email)
            ->setPassword('12345')
            ->setName('Name-test')
            ->setFirstname('First-')
            ->setBirthdate(new \DateTimeImmutable())
            ->setAddress('5 rue du midi')
            ->setZipcode(75015);

        $account = new Account();
        $account->setStatus('pending');
        $client->setAccount($account);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $clientDispatcher = new ClientDispatcher($this->entityManager);
        $clientDispatcher->dispatch($client);

        $resultUser = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client-test@bankin.net']);
        $resultBanker = $resultUser->getBanker();

        $this->assertEquals('banker-0@bankin.net', $resultBanker->getEmail());
    }

    public function testDispatchWithTwoClients(){
        $this->createBankers();

        for($i = 0; $i <= 2; $i++){
            $client = new Client();
            $email = 'client-' . $i .'@bankin.net';
            $client->setEmail($email)
                ->setPassword('12345')
                ->setName('Name-' . $i)
                ->setFirstname('First-')
                ->setBirthdate(new \DateTimeImmutable())
                ->setAddress('5 rue du midi')
                ->setZipcode(75015);
            $account = new Account();
            $account->setStatus('pending');
            $client->setAccount($account);

            $this->entityManager->persist($client);
        }

        $this->entityManager->flush();

        $clients = $this->entityManager->getRepository(Client::class)->findAll();

        $clientDispatcher = new ClientDispatcher($this->entityManager);
        $clientDispatcher->dispatch($clients[0]);

        $resultUser = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client-0@bankin.net']);
        $resultBanker = $resultUser->getBanker();

        $this->assertEquals('banker-0@bankin.net', $resultBanker->getEmail());

        $clientDispatcher->dispatch($clients[1]);

        $resultUser = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => 'client-1@bankin.net']);
        $resultBanker = $resultUser->getBanker();
        $this->assertNotEquals('banker-0@bankin.net', $resultBanker->getEmail());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
