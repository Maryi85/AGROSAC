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
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'boundary' => ['nullable', 'json'],
            'area' => ['required', 'numeric', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del lote es obligatorio.',
            'name.unique' => 'Ya existe un lote con este nombre.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'location.max' => 'La ubicación no puede tener más de 255 caracteres.',
            'area.required' => 'El área del lote es obligatoria.',
            'area.numeric' => 'El área debe ser un número.',
            'area.min' => 'El valor debe ser superior o igual a 1.',
            'status.required' => 'El estado del lote es obligatorio.',
            'status.in' => 'El estado debe ser activo o inactivo.',
        ];
    }
}


