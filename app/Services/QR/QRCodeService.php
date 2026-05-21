<?php

namespace App\Services\QR;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    public function generate(string $uuid): string
    {
        $svg = QrCode::format('svg')->size(200)->generate($uuid);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function sign(string $uuid): string
    {
        return hash_hmac('sha256', $uuid, config('app.key'));
    }

    public function validate(string $uuid, string $signature): bool
    {
        return hash_equals($this->sign($uuid), $signature);
    }

    public function validateSignature(string $uuid, string $signature): bool
    {
        return hash_equals($this->sign($uuid), $signature);
    }
}
