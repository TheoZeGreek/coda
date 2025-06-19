<?php

declare(strict_types=1);

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
        $document = new class($expected) {
            private array $data;
            public function __construct(array $data) { $this->data = $data; }
            public function getText(): string { return json_encode($this->data); }
        };
        $parser = new class($document) {
            private $doc;
            public function __construct($doc) { $this->doc = $doc; }
            public function parseFile(string $path) { return $this->doc; }
        };
        $result = Parser::fromPdf('dummy.pdf', $parser);
        $this->assertSame($expected, $result);
    }

    public function testFromPdfOcr(): void
    {
        $expected = [
            'iban' => 'BE99',
            'opening_balance' => 10,
            'closing_balance' => 10,
            'operations' => [],
        ];
        $document = new class {
            public function getText(): string { return ''; }
        };
        $parser = new class($document) {
            private $doc;
            public function __construct($doc) { $this->doc = $doc; }
            public function parseFile(string $path) { return $this->doc; }
        };
        $ocr = function (string $image) use ($expected): string {
            return json_encode($expected);
        };
        $result = Parser::fromPdf('dummy.pdf', $parser, $ocr);
        $this->assertSame($expected, $result);
    }
}
