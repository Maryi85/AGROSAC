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
            'description' => ['nullable', 'string', 'max:1000'],
            'variety' => ['nullable', 'string', 'max:255'],
            'yield_per_hectare' => ['nullable', 'numeric', 'min:0'],
            'plot_id' => ['nullable', 'exists:plots,id'],
            'status' => ['required', 'in:active,inactive'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del cultivo es obligatorio.',
            'name.unique' => 'Ya existe un cultivo con este nombre.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'description.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'variety.max' => 'La variedad no puede tener más de 255 caracteres.',
            'yield_per_hectare.numeric' => 'El rendimiento por hectárea debe ser un número.',
            'yield_per_hectare.min' => 'El rendimiento por hectárea no puede ser negativo.',
            'plot_id.required' => 'Debe seleccionar un lote.',
            'plot_id.exists' => 'El lote seleccionado no es válido.',
            'status.required' => 'El estado del cultivo es obligatorio.',
            'status.in' => 'El estado debe ser activo o inactivo.',
        ];
    }
}