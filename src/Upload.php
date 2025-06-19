<?php

declare(strict_types=1);

namespace Coda;

use RuntimeException;

final class Upload
{
    /**
     * Validate an uploaded file.
     *
     * @param array $file The $_FILES entry for the upload.
     *
     * @throws RuntimeException If the upload is invalid.
     */
    public static function validate(array $file): void
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload error');
        }

        if (($file['size'] ?? 0) > 10 * 1024 * 1024) {
            throw new RuntimeException('File too large');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            throw new RuntimeException('Unable to open finfo');
        }
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if ($mime !== 'application/pdf') {
            throw new RuntimeException('Invalid file type');
        }
    }
}
