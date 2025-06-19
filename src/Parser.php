<?php

declare(strict_types=1);

namespace Coda;

use RuntimeException;

final class Parser
{
    /**
     * Parse a PDF statement and return the operation data.
     *
     * @param string $path Path to the PDF file.
     * @param \Smalot\PdfParser\Parser|null $pdfParser Optional PDF parser for testing.
     * @param callable|null $ocr Optional OCR callback receiving image path and returning JSON text.
     */
    public static function fromPdf(string $path, ?\Smalot\PdfParser\Parser $pdfParser = null, ?callable $ocr = null): array
    {
        $pdfParser = $pdfParser ?? new \Smalot\PdfParser\Parser();
        try {
            $document = $pdfParser->parseFile($path);
            $text = trim($document->getText());
        } catch (\Throwable $e) {
            $text = '';
        }

        if ($text !== '') {
            return self::decode($text);
        }

        if (!is_callable($ocr)) {
            $ocr = [self::class, 'callOpenAi'];
        }

        $images = self::pdfToImages($path);
        $result = '';
        foreach ($images as $img) {
            $result .= $ocr($img);
        }

        return self::decode($result);
    }

    private static function pdfToImages(string $path): array
    {
        if (!class_exists('\Imagick')) {
            throw new RuntimeException('Imagick extension required for OCR path');
        }
        $imagick = new \Imagick();
        $imagick->readImage($path);
        $images = [];
        foreach ($imagick as $page) {
            $page->setImageFormat('png');
            $tmp = tempnam(sys_get_temp_dir(), 'ocr') . '.png';
            $page->writeImage($tmp);
            $images[] = $tmp;
        }
        return $images;
    }

    private static function callOpenAi(string $imagePath): string
    {
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        $headers = [
            'Authorization: Bearer ' . getenv('OPENAI_API_KEY'),
            'Content-Type: application/json',
        ];
        $body = [
            'model' => 'gpt-4-vision-preview',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => 'Extract bank statement data as JSON'],
                        ['type' => 'image_url', 'image_url' => [
                            'url' => 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath)),
                        ]],
                    ],
                ],
            ],
        ];
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body),
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new RuntimeException('OpenAI request failed: ' . curl_error($ch));
        }
        curl_close($ch);
        $data = json_decode($response, true);
        return $data['choices'][0]['message']['content'] ?? '';
    }

    private static function decode(string $text): array
    {
        $data = json_decode($text, true);
        if (!is_array($data)) {
            throw new RuntimeException('Unable to decode statement');
        }
        return $data;
    }
}
