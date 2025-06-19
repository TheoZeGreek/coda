<?php

declare(strict_types=1);

namespace Coda;

final class CodaBuilder
{
    public static function build(array $data): string
    {
        $lines = [];
        $seq = '0001';
        $lines[] = self::fixed(
            '0' . str_pad('010124', 6) .
            str_pad($data['iban'], 12) .
            ' ' .
            str_pad('', 109)
        );
        $lines[] = self::fixed('1' . str_pad('000000', 6) . str_pad('', 121));
        foreach ($data['operations'] as $op) {
            $lines[] = self::fixed(
                '21' .
                str_pad($op['date'], 6) .
                str_pad($op['label'], 100) .
                str_pad(number_format((float) $op['amount'], 2, ',', ''), 20)
            );
            $seq = sprintf('%04d', ((int)$seq + 1) % 10000);
        }
        $lines[] = self::fixed('8' . str_pad('010124', 6) . str_pad('', 121));
        $lines[] = self::fixed('9' . str_pad('', 127));
        return implode("\r\n", $lines) . "\r\n";
    }

    private static function fixed(string $line): string
    {
        return str_pad(substr($line, 0, 128), 128);
    }
}
