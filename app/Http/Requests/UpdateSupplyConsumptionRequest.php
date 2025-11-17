<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplyConsumptionRequest extends FormRequest
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
            'supply_id' => ['required', 'exists:supplies,id'],
            'crop_id' => ['nullable', 'exists:crops,id'],
            'plot_id' => ['nullable', 'exists:plots,id'],
            'task_id' => ['nullable', 'exists:tasks,id'],
            'qty' => ['required', 'numeric', 'min:0.001'],
            'used_at' => ['required', 'date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'supply_id.required' => 'Debe seleccionar un insumo.',
            'supply_id.exists' => 'El insumo seleccionado no existe.',
            'crop_id.exists' => 'El cultivo seleccionado no existe.',
            'plot_id.exists' => 'El lote seleccionado no existe.',
            'task_id.exists' => 'La tarea seleccionada no existe.',
            'qty.required' => 'La cantidad es obligatoria.',
            'qty.numeric' => 'La cantidad debe ser un número.',
            'qty.min' => 'La cantidad debe ser mayor a 0.',
            'used_at.required' => 'La fecha de uso es obligatoria.',
            'used_at.date' => 'La fecha de uso debe ser una fecha válida.',
        ];
    }
}