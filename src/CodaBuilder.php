<?php

declare(strict_types=1);

namespace Coda;

final class CodaBuilder
{
    public static function build(array $data): string
    {
        $seq = 1;
        $lines = [];

        // Record type 0 - file header
        $lines[] = self::fixed(
            '0' .
            str_pad(date('dmy'), 6) .
            str_pad(substr($data['iban'], 0, 16), 16) .
            str_pad('', 101),
            $seq++
        );

        // Record type 1 - opening balance
        $lines[] = self::fixed(
            '1' .
            str_pad(self::amount((float) $data['opening_balance']), 20) .
            str_pad('', 103),
            $seq++
        );

        // Record type 2.x - operations
        foreach ($data['operations'] as $op) {
            $lines[] = self::fixed(
                '21' .
                str_pad($op['date'], 6) .
                str_pad($op['label'], 96) .
                str_pad(self::amount((float) $op['amount']), 20),
                $seq++
            );
        }

        // Record type 8 - closing balance
        $lines[] = self::fixed(
            '8' .
            str_pad(self::amount((float) $data['closing_balance']), 20) .
            str_pad('', 103),
            $seq++
        );

        // Record type 9 - file trailer
        $lines[] = self::fixed(
            '9' . str_pad('', 123),
            $seq++
        );

        return implode("\r\n", $lines) . "\r\n";
    }

    private static function fixed(string $content, int $seq): string
    {
        $line = str_pad(substr($content, 0, 124), 124);
        $line .= sprintf('%04d', $seq <= 9999 ? $seq : 9999);
        return $line;
    }

    private static function amount(float $amount): string
    {
        $sign = $amount < 0 ? '-' : '+';
        $amount = abs($amount);
        return $sign . str_pad(number_format($amount, 2, ',', ''), 18, '0', STR_PAD_LEFT);
    }
}
