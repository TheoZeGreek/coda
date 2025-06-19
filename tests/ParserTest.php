<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Coda\Parser;

final class ParserTest extends TestCase
{
    public function testFromPdfText(): void
    {
        $expected = [
            'iban' => 'BE11',
            'opening_balance' => 1,
            'closing_balance' => 2,
            'operations' => [],
        ];
        $document = new class ($expected) extends \Smalot\PdfParser\Document {
            private array $data;

            public function __construct(array $data)
            {
                parent::__construct();
                $this->data = $data;
            }

            public function getText(?int $pageLimit = null): string
            {
                return json_encode($this->data);
            }
        };
        $parser = new class ($document) extends \Smalot\PdfParser\Parser {
            private $doc;

            public function __construct($doc)
            {
                $this->doc = $doc;
            }

            public function parseFile(string $filename): \Smalot\PdfParser\Document
            {
                return $this->doc;
            }
        };
        $result = Parser::fromPdf('dummy.pdf', $parser);
        $this->assertSame($expected, $result);
    }

    public function testFromPdfOcr(): void
    {
        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick not available');
        }

        $expected = [
            'iban' => 'BE99',
            'opening_balance' => 10,
            'closing_balance' => 10,
            'operations' => [],
        ];
        $document = new class extends \Smalot\PdfParser\Document {
            public function __construct()
            {
                parent::__construct();
            }

            public function getText(?int $pageLimit = null): string
            {
                return '';
            }
        };
        $parser = new class ($document) extends \Smalot\PdfParser\Parser {
            private $doc;

            public function __construct($doc)
            {
                $this->doc = $doc;
            }

            public function parseFile(string $filename): \Smalot\PdfParser\Document
            {
                return $this->doc;
            }
        };
        $ocr = function (string $image) use ($expected): string {
            return json_encode($expected);
        };
        $result = Parser::fromPdf('dummy.pdf', $parser, $ocr);
        $this->assertSame($expected, $result);
    }
}
