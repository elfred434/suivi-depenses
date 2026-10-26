<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ConnexionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array {
        return [
            'email.required' => 'le champs email est requis',
            'email.email' => 'le contenu du champs email ne correspond pas au format email',
            'password.required' => 'le mot de passe est requis',
            'password.string' => 'le champs mot de passe ne doit contenir que des caractères',
        ];
    }
}
