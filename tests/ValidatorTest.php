<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Coda\Validator;

final class ValidatorTest extends TestCase
{
    public function testValidData(): void
    {
        $data = [
            'iban' => 'BE68539007547034',
            'opening_balance' => 100,
            'closing_balance' => 100,
            'operations' => [],
        ];
        Validator::check($data);
        $this->assertTrue(true);
    }

    public function testInvalidCheckDigits(): void
    {
        $data = [
            'iban' => 'BE68539007547035',
            'opening_balance' => 100,
            'closing_balance' => 100,
            'operations' => [],
        ];

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('IBAN invalide');
        Validator::check($data);
    }

    public function testLongIban(): void
    {
        $method = new \ReflectionMethod(Validator::class, 'checkIban');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke(null, 'GB82WEST12345698765432'));
    }
}
