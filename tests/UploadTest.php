<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Coda\Upload;

final class UploadTest extends TestCase
{
    public function testRejectNonPdf(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmp, 'not a pdf');
        $file = [
            'name' => 'file.txt',
            'tmp_name' => $tmp,
            'size' => filesize($tmp),
            'error' => UPLOAD_ERR_OK,
        ];
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid file type');
        Upload::validate($file);
    }

    public function testRejectLargeFile(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmp, str_repeat('a', 10 * 1024 * 1024 + 1));
        $file = [
            'name' => 'file.pdf',
            'tmp_name' => $tmp,
            'size' => filesize($tmp),
            'error' => UPLOAD_ERR_OK,
        ];
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File too large');
        Upload::validate($file);
    }
}
