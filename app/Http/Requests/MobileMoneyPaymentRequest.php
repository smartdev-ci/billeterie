<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MobileMoneyPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'order_uuid'        => ['required', 'string', 'uuid'],
            'payment_reference' => ['required', 'string', 'max:50'],
            'status'            => ['required', Rule::in(['success', 'failed'])],
        ];
    }
}