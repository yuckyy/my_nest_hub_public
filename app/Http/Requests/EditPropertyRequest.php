<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditPropertyRequest extends FormRequest
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
            'type' => 'required|integer|min:1',
            'address' => 'required|string|min:1|max:255',
            'city' => 'required|string|min:1|max:255',
            'state' => 'required|integer|min:1',
            'zip' => 'required|string|max:5',
            'date' => 'nullable|string|min:1',
            'purchased_amount' => 'nullable|string|max:14',
            'current_amount' => 'nullable|string|max:14',
            'units.*.name' => 'required|string|min:1|max:255',
            // 'units.*.square' => 'required|string|max:9',
            'units.*.square' => 'nullable|string|max:9',
            'units.*.bedrooms' => 'required|string|min:1',
            'units.*.full_bathrooms' => 'required|string|min:0',
            'units.*.half_bathrooms' => 'required|string|min:0',
            'units.*.description' => 'max:65000',
            'property_photo' => 'file|nullable',
            'property_gallery.*' => 'file|nullable',
        ];
    }

    public function attributes()
    {
        return [
            'units.*.name' => 'unit name',
            'units.*.square' => 'unit square',
            'units.*.bedrooms' => 'unit bedrooms',
            'units.*.full_bathrooms' => 'unit full bathrooms',
            'units.*.half_bathrooms' => 'unit half bathrooms',
            'property_gallery.*' => 'property gallery',
        ];
    }
}
