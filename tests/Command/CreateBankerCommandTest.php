<?php

namespace App\Tests\Command;

use App\Entity\Banker;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Application
     */
    private $application;

    public function setUp(): void
    {
        $kernel = static::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->application = new Application($kernel);
    }

    public function testExecute()
    {
        $email = 'johndoe@inter.net';

        $command = $this->application->find('app:create-banker');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            // pass arguments to the helper
            'email' => $email,
            'password' => 'johdoe'
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Banker successfully generated!', $output);

        $repo = $this->entityManager
            ->getRepository(Banker::class);

        $banker = $repo
            ->findOneBy(['email' => $email]);

        $this->assertSame($email, $banker->getEmail());

        $totalBankers = $repo->createQueryBuilder('b')
            ->select('count(b.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(1, $totalBankers);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}