<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCropRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:crops,name'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}


