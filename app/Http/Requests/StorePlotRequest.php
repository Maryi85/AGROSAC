<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:plots,name'],
            'location' => ['nullable', 'string', 'max:255'],
            'area' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}


