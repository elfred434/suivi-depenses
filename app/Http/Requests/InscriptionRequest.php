<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InscriptionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100' ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed']
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'Le champs nom est requis',
            'name.string' => 'Le champs nom ne doit contenir que des caractères',
            'name.max' => 'Le nombre de caractères dans le champs ne doit dépasser les :max caractères',
            'email.required' => 'le champs email est requis',
            'email.email' => 'les information à l\'intérieur de l\'email ne respectent pas le format',
            'email.unique' => 'Cette email existe déjà ',
            'email.max' => 'le nombre de caractères à l\'intérieur du champs email ne doit dépasser les :max caractères',
            'password.required' => 'le champs password est requis',
            'password.string' => 'le champs ne doit contenir que des caractères',
            'password.min' => 'le nombre de caractères minimale pour le mot de passe est :min ',
            'password.confirmed' => 'les deux mot de passe ne correspondent pas',
        ];
    }
}
