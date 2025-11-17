<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplyRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:supplies,name,' . $this->route('supply')->id],
            'unit' => ['required', 'string', 'in:kg,lt,unit,g,ml,lb,gal'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del insumo es obligatorio.',
            'name.unique' => 'Ya existe un insumo con este nombre.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'unit.required' => 'La unidad de medida es obligatoria.',
            'unit.in' => 'La unidad de medida seleccionada no es válida.',
            'unit_cost.numeric' => 'El costo por unidad debe ser un número.',
            'unit_cost.min' => 'El costo por unidad no puede ser negativo.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }
}