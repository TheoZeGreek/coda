<?php

declare(strict_types=1);

namespace Coda;

use Exception;

final class Parser
{
    public static function fromPdf(string $path): array
    {
        // Placeholder logic: in real implementation we would use Smalot\PdfParser
        // and OpenAI API to parse PDF content.
        // Here we return a dummy structure for example purposes.
        return [
            'company' => 'Example SA',
            'iban' => 'BE68539007547034',
            'opening_balance' => 1000.00,
            'closing_balance' => 1100.00,
            'operations' => [
                ['date' => '010124', 'label' => 'Paiement', 'amount' => 100.00],
            ],
        ];
    }
}
