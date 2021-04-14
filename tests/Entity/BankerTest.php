<?php


namespace App\Tests\Entity;

use App\Entity\Banker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BankerTest extends KernelTestCase
{

    private function getEntity(): Banker
    {
        return  (new Banker())->setEmail('banker@bankin.fr');
    }

    private function assertHasErrors(Banker $banker, int $number = 0)
    {
        self::bootKernel();

        $error = self::$container->get(ValidatorInterface::class)->validate($banker);
        $this->assertCount($number, $error);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidBankerEntity()
    {
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
        $this->assertHasErrors($this->getEntity()->setEmail('test'), 1);
    }
}