<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskExecutionRequest extends FormRequest
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
        $rules = [
            'status' => ['required', 'string', Rule::in(['pending', 'in_progress', 'completed'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        // Si el estado es "completed", aplicamos validaciones estrictas
        if ($this->input('status') === 'completed') {
            // Obtenemos el tipo de pago de la tarea desde la ruta o request
            $task = $this->route('task'); // Asumiendo que la ruta tiene {task}
            
            if ($task && $task->payment_type === 'por_horas') {
                // Para tareas por horas: hours_worked debe ser > 0
                $rules['hours_worked'] = [
                    'required',
                    'numeric',
                    'min:0.1',
                    'max:24', // Máximo 24 horas por día (ajusta según necesites)
                ];
            } elseif ($task && $task->payment_type === 'por_cantidad') {
                // Para tareas por cantidad: amount_collected debe ser > 0
                $rules['amount_collected'] = [
                    'required',
                    'numeric',
                    'min:0.1',
                    'max:100000', // Ajusta el máximo según tus necesidades
                ];
            }
        } else {
            // Si no está completada, los campos son opcionales pero deben ser numéricos si se envían
            $rules['hours_worked'] = ['nullable', 'numeric', 'min:0', 'max:24'];
            $rules['amount_collected'] = ['nullable', 'numeric', 'min:0', 'max:100000'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'hours_worked.required' => 'Debes registrar las horas trabajadas para finalizar la tarea.',
            'hours_worked.min' => 'Las horas trabajadas deben ser mayor a 0 para finalizar la tarea.',
            'hours_worked.numeric' => 'Las horas trabajadas deben ser un número válido.',
            'hours_worked.max' => 'Las horas trabajadas no pueden exceder 24 horas.',
            
            'amount_collected.required' => 'Debes registrar la cantidad recolectada para finalizar la tarea.',
            'amount_collected.min' => 'La cantidad recolectada debe ser mayor a 0 para finalizar la tarea.',
            'amount_collected.numeric' => 'La cantidad recolectada debe ser un número válido.',
            'amount_collected.max' => 'La cantidad recolectada excede el límite permitido.',
            
            'status.required' => 'El estado de la tarea es requerido.',
            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
