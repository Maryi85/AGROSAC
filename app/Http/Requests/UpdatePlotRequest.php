<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $plotId = $this->route('plot')->id ?? null;

        return [
            'name' => ['required', 'string', 'max:255', 'unique:plots,name,' . $plotId],
            'location' => ['nullable', 'string', 'max:255'],
            'area' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}


