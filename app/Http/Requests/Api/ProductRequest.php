<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'companies' => 'sometimes|array',
            'companies.*.id' => 'required_with:companies|integer|exists:companies,id',
            'companies.*.price' => 'nullable|numeric|min:0|max:999999.99',
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
            'name.string' => 'El nombre del producto debe ser texto.',
            'name.max' => 'El nombre del producto no puede exceder 255 caracteres.',

            'description.required' => 'La descripción del producto es obligatoria.',
            'description.string' => 'La descripción del producto debe ser texto.',
            'description.max' => 'La descripción no puede exceder 1000 caracteres.',

            'companies.array' => 'Las compañías deben ser un arreglo válido.',
            'companies.*.id.required_with' => 'El ID de la compañía es obligatorio.',
            'companies.*.id.integer' => 'El ID de la compañía debe ser un número entero.',
            'companies.*.id.exists' => 'La compañía seleccionada no existe.',

            'companies.*.price.numeric' => 'El precio debe ser un valor numérico.',
            'companies.*.price.min' => 'El precio debe ser mayor o igual a 0.',
            'companies.*.price.max' => 'El precio no puede exceder 999,999.99.',
        ];
    }

    /**
     * Handle a failed validation attempt for API requests.
     * Siempre devuelve respuesta JSON para APIs.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
                'data' => null
            ], 422)
        );
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'description' => 'descripción',
            'companies' => 'compañías',
            'companies.*.id' => 'ID de compañía',
            'companies.*.price' => 'precio',
        ];
    }

}
