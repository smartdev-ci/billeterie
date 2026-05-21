<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name'   => ['required', 'string', 'max:255'],
            'customer_email'  => ['required', 'email', 'max:255'],
            'customer_phone'  => ['required', 'string', 'max:20', 'regex:/^[0-9\s\-\+]+$/'],
            'quantity'        => ['required', 'integer', 'min:1', 'max:10'],
            'mobile_provider' => ['required', Rule::in(['mtn', 'orange', 'moov'])],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'Format de numéro invalide.',
            'mobile_provider.in'   => 'Opérateur Mobile Money non supporté.',
        ];
    }
}