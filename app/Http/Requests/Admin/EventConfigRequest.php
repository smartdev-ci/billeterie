<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EventConfigRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'event_date'    => 'required|date',
            'location'      => 'required|string|max:255',
            'max_tickets'   => 'required|integer|min:1',
            'status'        => 'required|in:draft,active,sold_out,closed',
        ];
    }
}