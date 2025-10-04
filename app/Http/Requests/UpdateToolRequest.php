<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateToolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:herramientas_manuales,herramientas_electricas,equipos_agricolas,vehiculos,otros'],
            'status' => ['required', 'string', 'in:operational,damaged,lost,retired'],
            'total_qty' => ['required', 'integer', 'min:0'],
            'available_qty' => ['required', 'integer', 'min:0', 'lte:total_qty'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la herramienta es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'category.required' => 'La categoría es obligatoria.',
            'category.in' => 'La categoría seleccionada no es válida.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            'total_qty.required' => 'La cantidad total es obligatoria.',
            'total_qty.integer' => 'La cantidad total debe ser un número entero.',
            'total_qty.min' => 'La cantidad total no puede ser negativa.',
            'available_qty.required' => 'La cantidad disponible es obligatoria.',
            'available_qty.integer' => 'La cantidad disponible debe ser un número entero.',
            'available_qty.min' => 'La cantidad disponible no puede ser negativa.',
            'available_qty.lte' => 'La cantidad disponible no puede ser mayor que la cantidad total.',
        ];
    }
}