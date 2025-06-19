<?php

declare(strict_types=1);

namespace Tests;

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
                ['date' => '010124', 'label' => 'Test operation', 'amount' => 100],
            ],
        ];

        $cod = CodaBuilder::build($data);
        $this->assertStringEndsWith("\r\n", $cod);

        $lines = explode("\r\n", trim($cod));
        $this->assertCount(5, $lines);

        foreach ($lines as $i => $line) {
            $this->assertSame(128, strlen($line));
            $seq = sprintf('%04d', $i + 1);
            $this->assertSame($seq, substr($line, -4));
        }

        $this->assertSame('0', $lines[0][0]);
        $this->assertSame('1', $lines[1][0]);
        $this->assertSame('2', $lines[2][0]);
        $this->assertSame('8', $lines[3][0]);
        $this->assertSame('9', $lines[4][0]);

        $this->assertSame(16, strlen(trim(substr($lines[0], 7, 16))));
        $this->assertSame(6, strlen(substr($lines[2], 2, 6)));
    }
}
