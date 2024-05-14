<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

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
            'id' => ['nullable', 'numeric'],
            'name' => ['required'],
            'modules' => ['required', 'array'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            //'id.required' => 'El campo id es requerido',
            'id.nullable' => 'El campo id, puede ser null o numerico',
            'id.numeric' => 'El campo id, puede ser null o numerico',
            'name.required' => 'El campo nombre es requerido',
            'modules.required' => 'El campo modules es requerido',
            'modules.array' => 'El campo modules debe ser un array',
            'description.string' => 'El campo description pueder ser texto o nulo',
            'description.nullable' => 'El campo description pueder ser texto o nulo',
        ];
    }
}
