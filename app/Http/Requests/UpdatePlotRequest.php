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
            'location' => ['required', 'string', 'max:255'],
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
            'location.required' => 'La ubicación del lote es obligatoria.',
            'location.max' => 'La ubicación no puede tener más de 255 caracteres.',
            'area.required' => 'El área del lote es obligatoria.',
            'area.numeric' => 'El área debe ser un número.',
            'area.min' => 'El valor debe ser superior o igual a 1.',
            'status.required' => 'El estado del lote es obligatorio.',
            'status.in' => 'El estado debe ser activo o inactivo.',
        ];
    }
}


