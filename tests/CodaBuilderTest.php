<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Coda\CodaBuilder;

final class CodaBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $data = [
            'iban' => 'BE68539007547034',
            'opening_balance' => 1000,
            'closing_balance' => 1100,
            'operations' => [
                ['date' => '010124', 'label' => 'Test', 'amount' => 100],
            ],
        ];
        $cod = CodaBuilder::build($data);
        $this->assertStringContainsString("\r\n", $cod);
    }
}
