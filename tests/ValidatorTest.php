<?php

declare(strict_types=1);

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
}
