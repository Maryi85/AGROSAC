<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
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
            'due_at' => ['nullable', 'date'],
            'condition_return' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'string', 'in:out,returned,lost,damaged'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'due_at.date' => 'La fecha de devolución debe ser una fecha válida.',
            'condition_return.string' => 'La condición de devolución debe ser texto.',
            'condition_return.max' => 'La condición de devolución no puede tener más de 500 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }
}