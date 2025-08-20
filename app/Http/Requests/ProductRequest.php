<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // La autorización específica puede manejarse vía middleware o políticas
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'companies' => 'sometimes|array',
            'companies.*' => 'array',
            'companies.*.id' => 'sometimes|integer|exists:companies,id',
            'companies.*.price' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'description.required' => 'La descripción del producto es obligatoria.',
            'companies.array' => 'El campo compañías debe ser un arreglo.',
            'companies.*.exists' => 'Una o más compañías seleccionadas no existen.',
        ];
    }
}
