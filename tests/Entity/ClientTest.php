<?php


namespace App\Tests\Entity;


use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientTest extends KernelTestCase
{
    private function getEntity(): Client
    {
        return  (new Client())->setEmail('client@bankin.fr')
            ->setPassword('123456')->setName('test')
            ->setFirstname('name test')->setBirthdate(new \DateTime())
            ->setAddress('rue du paradi')->setZipcode(75957)->setCity('paris');
    }

    private function assertHasErrors(Client $client, int $number = 0)
    {
        self::bootKernel();

        $error = self::$container->get(ValidatorInterface::class)->validate($client);
        $this->assertCount($number, $error);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidClientEntity()
    {
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
        $this->assertHasErrors($this->getEntity()->setEmail('test'), 1);
        $this->assertHasErrors($this->getEntity()->setPassword(''), 1);
        $this->assertHasErrors($this->getEntity()->setName(''), 1);
        $this->assertHasErrors($this->getEntity()->setFirstname(''), 1);
        $this->assertHasErrors($this->getEntity()->setAddress(''), 1);
    }
}