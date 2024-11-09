<?php

namespace App\Http\Requests\Campus;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCampusRequest extends FormRequest
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
            'campusId' => ['nullable'],
            'name' => ['required'],
            'email' => ['email:rfc', 'nullable'],
            'phone' => ['nullable'],
            'address' => ['required'],
            'observations' => ['nullable']
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
            'name.required' => 'El campo name es requerido',
            'email.email' => 'El campo email debe ser una dirección email valida',
            'address.required' => 'El campo Address es requerido',
        ];
    }
}
