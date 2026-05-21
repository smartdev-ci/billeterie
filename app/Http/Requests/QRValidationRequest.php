<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QRValidationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'ticket_uuid' => ['required', 'uuid', 'exists:tickets,uuid'],
            'signature'   => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'ticket_uuid.exists' => 'Billet introuvable.',
            // 'signature.size'     => 'Signature QR invalide.',
        ];
    }
}