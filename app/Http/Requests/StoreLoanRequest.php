<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
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
            'tool_id' => ['required', 'exists:tools,id'],
            'user_id' => ['required', 'exists:users,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'due_at' => ['nullable', 'date', 'after:today'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tool_id.required' => 'Debe seleccionar una herramienta.',
            'tool_id.exists' => 'La herramienta seleccionada no existe.',
            'user_id.required' => 'Debe seleccionar un trabajador.',
            'user_id.exists' => 'El trabajador seleccionado no existe.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad debe ser al menos 1.',
            'due_at.date' => 'La fecha de devolución debe ser una fecha válida.',
            'due_at.after' => 'La fecha de devolución debe ser posterior a hoy.',
        ];
    }
}