<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLedgerEntryRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:income,expense'],
            'category' => ['required', 'string', 'in:venta_cultivos,servicios_agricolas,subsidios,otros_ingresos,insumos,mano_obra,maquinaria,fertilizantes,pesticidas,riego,otros_gastos'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'occurred_at' => ['required', 'date'],
            'crop_id' => ['nullable', 'exists:crops,id'],
            'plot_id' => ['nullable', 'exists:plots,id'],
            'reference' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'El tipo de movimiento es obligatorio.',
            'type.in' => 'El tipo de movimiento debe ser ingresos o gastos.',
            'category.required' => 'La categoría es obligatoria.',
            'category.in' => 'La categoría seleccionada no es válida.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.numeric' => 'El monto debe ser un número.',
            'amount.min' => 'El monto debe ser mayor a 0.',
            'occurred_at.required' => 'La fecha es obligatoria.',
            'occurred_at.date' => 'La fecha debe ser una fecha válida.',
            'crop_id.exists' => 'El cultivo seleccionado no existe.',
            'plot_id.exists' => 'El lote seleccionado no existe.',
            'reference.max' => 'La referencia no puede tener más de 255 caracteres.',
        ];
    }
}