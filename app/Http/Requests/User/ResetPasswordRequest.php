<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'idUser' => 'required',
            'password' => 'required|min:7|confirmed',
            'password_confirmation' => 'required|min:7',
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
            'idUser.required' => 'El valor Id es requerido',
            'password.required' => 'El campo password es requerido',
            'password.min' => 'El campo password debe tener minimo 7 caracteres',
            'password.confirmed' => 'El campo password debe ser confirmado',
        ];
    }
}
