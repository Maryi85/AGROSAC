<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCropRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $cropId = $this->route('crop')->id ?? null;
        return [
            'name' => ['required', 'string', 'max:255', 'unique:crops,name,' . $cropId],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}


