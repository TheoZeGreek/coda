<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/autoload.php';

use Coda\Parser;
use Coda\CodaBuilder;
use Coda\Validator;

header('Access-Control-Allow-Origin: *');

$route = $_GET['route'] ?? '';
if ($route === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['file'] ?? null;
    if (!$file) {
        http_response_code(400);
        exit;
    }
    $path = __DIR__ . '/tmp/' . uniqid('pdf_', true) . '.pdf';
    move_uploaded_file($file['tmp_name'], $path);
    $data = Parser::fromPdf($path);
    echo json_encode($data);
    unlink($path);
    exit;
}

if ($route === 'build' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    try {
        Validator::check($data);
        $content = CodaBuilder::build($data);
        header('Content-Type: text/plain; charset=windows-1252');
        header('X-Filename: ' . date('Ymd') . "_{$data['iban']}_0001.cod");
        echo $content;
    } catch (RuntimeException $e) {
        http_response_code(400);
        echo $e->getMessage();
    }
    exit;
}

http_response_code(404);
