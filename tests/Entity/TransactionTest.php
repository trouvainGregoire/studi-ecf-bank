<?php


namespace App\Tests\Entity;


use App\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransactionTest extends KernelTestCase
{
    private function getEntity(): Transaction
    {
        return  (new Transaction())->setAmount(1);
    }

    private function assertHasErrors(Transaction $transaction, int $number = 0)
    {
        self::bootKernel();

        $error = self::$container->get(ValidatorInterface::class)->validate($transaction);
        $this->assertCount($number, $error);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }
}