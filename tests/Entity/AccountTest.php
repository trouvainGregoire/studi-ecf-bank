<?php

namespace App\Tests\Entity;

use App\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccountTest extends KernelTestCase
{

    private function getEntity(): Account
    {
        return  (new Account());
    }

    private function assertHasErrors(Account $account, int $number = 0)
    {
        self::bootKernel();
        $error = self::$container->get(ValidatorInterface::class)->validate($account);
        $this->assertCount($number, $error);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }
}