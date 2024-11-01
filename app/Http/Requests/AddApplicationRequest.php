<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'firstname' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'dob' => ['required', 'date', 'date_format:Y-m-d'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'employmentAndlIncomes.*.employment' => ['nullable', 'string'],
            'employmentAndlIncomes.*.income' => ['required', 'string'],
            'residenceHistories.*.start_date' => ['nullable','date', 'date_format:Y-m-d'],
            'residenceHistories.*.address' => ['nullable', 'string'],
            'residenceHistories.*.city' => ['nullable', 'string'],
            'residenceHistories.*.state_id' => ['nullable', 'integer'],
            'incomes.*.description' => ['required', 'string'],
            'incomes.*.amount' => ['required', 'string'],
            'references.*.name' => ['nullable', 'string'],
            'references.*.email' => ['nullable', 'email'],
            'references.*.phone' => ['nullable', 'string'],
            'pets.*.pets_type_id' => ['required', 'integer'],
            'pets.*.description' => ['required', 'string'],
            'property_id' => ['integer'],
            'unit_id' => ['integer'],
            'start_date' => ['date', 'date_format:Y-m-d'],
            'end_date' => ['sometimes', 'date', 'date_format:Y-m-d', 'nullable', 'greater_than_field:start_date'],
            'notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],

            'password' => ['sometimes', 'required', 'confirmed', 'min:8'],
            'password_confirmation' => ['sometimes', 'required', 'string', 'min:8', 'same:password']
        ];
    }

    public function attributes()
    {
        return [
            'employmentAndlIncomes.*.employment' => 'employment and incomes employment',
            'employmentAndlIncomes.*.income' => 'employment and incomes income',
            'residenceHistories.*.start_date' => 'residence history start date',
            'residenceHistories.*.address' => 'residence history address',
            'residenceHistories.*.city' => 'residence history city',
            'residenceHistories.*.state_id' => 'residence history state',
            'incomes.*.description' =>  'incomes description',
            'incomes.*.amount' => 'incomes amount',
            'references.*.name' => 'references name',
            'references.*.email' => 'references email',
            'references.*.phone' => 'references phone',
            'pets.*.pets_type_id' => 'pets type',
            'pets.*.description' =>  'pets description',
            'start_date' => 'start date',
            'property_id' => 'property',
            'unit_id' => 'unit',
            'end_date' => 'end date'
        ];
    }

    public function messages()
    {
        return [
            'employmentAndlIncomes.*.income.required' => 'Employment and income are required',
            'employmentAndlIncomes.*.employment.required' => 'Employment and income are required'
        ];
    }
}
