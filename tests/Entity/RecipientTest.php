<?php


namespace App\Tests\Entity;

use App\Entity\Recipient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RecipientTest extends KernelTestCase
{
    private function getEntity(): Recipient
    {
        return  (new Recipient())->setName('test')->setFirstname('firstname test');
    }

    private function assertHasErrors(Recipient $recipient, int $number = 0)
    {
        self::bootKernel();

        $error = self::$container->get(ValidatorInterface::class)->validate($recipient);
        $this->assertCount($number, $error);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidRecipientEntity()
    {
        $this->assertHasErrors($this->getEntity()->setName(''), 1);
        $this->assertHasErrors($this->getEntity()->setFirstname(''), 1);
    }
}