# CODA PDF Converter

This project converts Belgian bank statements in PDF format to CODA v2.4 files.

## Setup

1. Upload the repository to your OVH hosting (Apache + PHP 8.2).
2. Run `composer install` to install PHP dependencies.
3. Create a `.env` file with `OPENAI_API_KEY` containing your API key.
4. Ensure the `tmp/` folder is writable by the web server.
5. Access `public/index.html` to start using the tool.

## Dependencies

The application relies on a few Composer packages:

- `smalot/pdfparser` for parsing PDF statement files.
- `openai-php/client` for communicating with the OpenAI API.

### Without Composer

If you prefer not to rely on Composer in production, copy the library sources in
`lib/` and use the provided `lib/autoload.php`. Replace `vendor/autoload.php`
with `require 'lib/autoload.php';` in your entry points. You will lose automatic
updates, so manage dependencies manually.

## Development

- Source code lives under `src/`.
- Frontend assets are in `public/`.
- Unit tests use PHPUnit and can be run with `vendor/bin/phpunit`.
- Code style is enforced with `phpcs`.

## Example

An example PDF is provided in `tests/data/example.pdf` together with a generated `example.cod` file.
