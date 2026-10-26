<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class IndexDepensesRequest extends FormRequest
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
            'libelle' => ['nullable', 'string', 'max:100'],
            'categorie_id' => ['nullable', 'integer', 
            Rule::exists('categories', 'id')
            ->where('user_id', $this->user()->id)
            ],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => array_merge(
                ['nullable', 'date'],
                $this->filled('date_debut') ? ['after_or_equal:date_debut'] : [],
            ),
        ];
    }

    public function messages(): array {
        return [
            'libelle.string' => 'le champs libellé ne doit contenir que des caractères',
            'libelle.min' => 'le nombre de caractères minimale est de :min',
            'libelle.max' => 'le nombre de caractères maximale est de :max', 
            'categorie_id.exists' => 'La catégorie sélectionnées n\'existe pas ou vous appartient pas', 
            'categorie_id.integer' => 'la catégorie doit être un nombre entier',
            'date_debut.date' => 'La date de début doit être valide.',
            'date_fin.date' => 'La date de fin doit être valide.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            
            ];
    }
}
