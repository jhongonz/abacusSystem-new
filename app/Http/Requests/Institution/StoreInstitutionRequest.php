<?php

namespace App\Http\Requests\Institution;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInstitutionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'institutionId' => ['nullable'],
            'code' => ['nullable'],
            'name' => ['required'],
            'shortname' => ['required'],
            'address' => ['required'],
            'phone' => ['required'],
            'email' => ['required', 'email:rfc'],
            'observations' => ['nullable'],
            'token' => ['nullable'],
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
            'shortname.required' => 'El campo shortname es requerido',
            'email.required' => 'El campo email es requerido',
            'email.email' => 'El campo email debe ser una dirección email valida',
            'phone.required' => 'El campo phone es requerido',
        ];
    }
}
