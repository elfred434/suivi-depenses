<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreDepensesRequest extends FormRequest
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
            'libelle' => ['required', 'string', 'min:3', 'max:100'],
            'montant' => ['required', 'integer', 'min:1'],
            'date_depenses' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'categorie_id' => ['required', 'integer',
                Rule::exists('categories', 'id')
                    ->where('user_id', $this->user()->id)
            ]
        ];
    }

    public function messages(): array {
        return [
                'libelle.required' => 'le champs libelle est requis',
                'montant.required' => 'le champs montant est requis',
                'date_depenses.required' => 'La date de la dépense est obligatoire.',
                'date_depenses.date' => 'La date de la dépense doit être valide.',
                'libelle.string' => 'le champs libellé ne doit contenir que des caractères',
                'description.string' => 'le champs description ne doit contenir que des caractères',
                'description.max' => 'La description ne peut pas dépasser :max caractères.',
                'libelle.min' => 'le nombre de caractères minimale est de :min',
                'libelle.max' => 'le nombre de caractères maximale est de :max', 
                'montant.integer' => 'Le montant doit être un nombre entier en FCFA.',
                'montant.min' => 'Le montant doit être strictement positif.',
                'categorie_id.exists' => 'La catégorie sélectionnées n\'existe pas ou vous appartient pas', 

        ];
    }
}
