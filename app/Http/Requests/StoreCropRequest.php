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
            'description' => ['nullable', 'string', 'max:1000'],
            'variety' => ['nullable', 'string', 'max:255'],
            'yield_per_hectare' => ['nullable', 'numeric', 'min:0'],
            'plot_id' => [
                'required', 
                'exists:plots,id',
                function ($attribute, $value, $fail) {
                    // Verificar que el lote no tenga un cultivo activo
                    $hasActiveCrop = \App\Models\Crop::where('plot_id', $value)
                        ->where('status', 'active')
                        ->exists();
                    
                    if ($hasActiveCrop) {
                        $plot = \App\Models\Plot::find($value);
                        $plotName = $plot ? $plot->name : 'el lote seleccionado';
                        $fail("El lote '{$plotName}' ya tiene un cultivo activo. Solo se permite un cultivo activo por lote.");
                    }
                }
            ],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'],
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
            'photo.image' => 'El archivo debe ser una imagen válida.',
            'photo.mimes' => 'La imagen debe ser de tipo: JPEG, JPG, PNG o GIF.',
            'photo.max' => 'La imagen no puede ser mayor a 2MB (2048 KB).',
        ];
    }
}