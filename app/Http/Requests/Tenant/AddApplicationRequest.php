<?php

namespace App\Http\Requests\Tenant;

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
            'employmentAndlIncomes.*.employment' => ['required', 'string'],
            'employmentAndlIncomes.*.income' => ['required', 'string'],
            'residenceHistories.*.start_date' => ['required','date', 'date_format:Y-m-d'],
            'residenceHistories.*.address' => ['required', 'string'],
            'residenceHistories.*.city' => ['required', 'string'],
            'residenceHistories.*.state_id' => ['required', 'integer'],
            'incomes.*.description' => ['required', 'string'],
            'incomes.*.amount' => ['required', 'string'],
            'references.*.name' => ['required', 'string'],
            'references.*.email' => ['required', 'email'],
            'references.*.phone' => ['required', 'string'],
            'pets.*.pets_type_id' => ['required', 'integer'],
            'pets.*.description' => ['required', 'string'],
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
