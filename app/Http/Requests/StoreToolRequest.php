<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreToolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'foreman']);
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
            'description' => ['nullable', 'string', 'max:1000'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
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
            'description.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'brand.max' => 'La marca no puede tener más de 255 caracteres.',
            'model.max' => 'El modelo no puede tener más de 255 caracteres.',
            'serial_number.max' => 'El número de serie no puede tener más de 255 caracteres.',
        ];
    }
}