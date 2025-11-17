<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'plot_id' => 'nullable|exists:plots,id',
            'crop_id' => 'nullable|exists:crops,id',
            'assigned_to' => 'required|exists:users,id',
            'scheduled_for' => 'required|date',
            'payment_type' => 'required|in:hours,days,quantity',
            'hours' => 'required_if:payment_type,hours|nullable|numeric|min:0',
            'days' => 'required_if:payment_type,days|nullable|integer|min:1',
            'kilos' => 'required_if:payment_type,quantity|nullable|numeric|min:0',
            'price_per_hour' => 'required_if:payment_type,hours|nullable|numeric|min:0',
            'price_per_day' => 'required_if:payment_type,days|nullable|numeric|min:0',
            'price_per_kg' => 'required_if:payment_type,quantity|nullable|numeric|min:0',
            'total_payment' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'El tipo de tarea es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'assigned_to.required' => 'Debe asignar la tarea a un trabajador.',
            'assigned_to.exists' => 'El trabajador seleccionado no existe.',
            'scheduled_for.required' => 'La fecha programada es obligatoria.',
            'payment_type.required' => 'Debe seleccionar el tipo de pago.',
            'payment_type.in' => 'El tipo de pago debe ser por horas, días o cantidad.',
            'hours.required_if' => 'Las horas son obligatorias cuando el pago es por horas.',
            'days.required_if' => 'Los días son obligatorios cuando el pago es por días.',
            'kilos.required_if' => 'La cantidad es obligatoria cuando el pago es por cantidad.',
        ];
    }
}