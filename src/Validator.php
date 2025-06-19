<?php

declare(strict_types=1);

namespace Coda;

use RuntimeException;

final class Validator
{
    public static function check(array $data): void
    {
        if (!preg_match('/^BE\\d{14}$/', $data['iban']) || !self::checkIban($data['iban'])) {
            throw new RuntimeException('IBAN invalide');
        }
        $balance = $data['opening_balance'];
        foreach ($data['operations'] as $op) {
            $balance += (float) $op['amount'];
        }
        if (abs($balance - (float) $data['closing_balance']) > 0.01) {
            throw new RuntimeException('Solde non équilibré');
        }
    }

    private static function checkIban(string $iban): bool
    {
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);
        $numeric = preg_replace_callback(
            '/[A-Z]/',
            static function (array $match): string {
                return (string) (ord($match[0]) - 55);
            },
            $rearranged
        );

        $remainder = 0;
        $len = strlen($numeric);
        for ($i = 0; $i < $len; $i++) {
            $remainder = ($remainder * 10 + (int) $numeric[$i]) % 97;
        }

        return $remainder === 1;
    }
}
