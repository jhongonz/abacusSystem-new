<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreEmployeeRequest extends FormRequest
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
        $rules = [
            'employeeId' => ['nullable'],
            'userId' => ['nullable'],
            'identifier' => ['required'],
            'typeDocument' => ['required'],
            'name' => ['required'],
            'lastname' => ['required'],
            'email' => ['required','email:rfc'],
            'profile' => ['required'],
            'login' => ['required'],
        ];

        $userId = $this->request->get('userId');
        $password = $this->request->get('password');

        if (is_null($userId) || ($userId > 0 && isset($password))) {
            $rules['password'] = ['required','confirmed','min:7'];
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'identifier.required' => 'El campo identifier es requerido',
            'typeDocument.required' => 'El campo typeDocument es requerido',
            'name.required' => 'El campo name es requerido',
            'lastname.required' => 'El campo lastname es requerido',
            'email.required' => 'El campo email es requerido',
            'email.email' => 'El campo email debe ser una dirección email valida',
            'profile.required' => 'El campo profile es requerido',
            'login.required' => 'El campo login es requerido',
            'password.required' => 'El campo password es requerido',
            'password.confirmed' => 'El campo password debe ser confirmado',
            'password.min' => 'El campo password debe ser minimo de 7 caracteres',
        ];
    }
}
