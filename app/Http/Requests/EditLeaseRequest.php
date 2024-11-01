<?php

namespace App\Http\Requests;

use App\Models\Lease;
use Illuminate\Foundation\Http\FormRequest;

class EditLeaseRequest extends FormRequest
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

    public function rules() {

        $data = $this->request->all();

        if ($data['type'] == "movein-amount") $data['type'] = "numeric";
        if ($data['type'] == "movein-memo") $data['type'] = "string";
        if ($data['type'] == "movein-date") $data['type'] = "date";

        $valueRules = ['required', $data['type']];
        if (!empty($data['max']) && $data['type'] == 'integer') $valueRules[] = 'max:' . $data['max'];
        if ($data['type'] == 'numeric') $valueRules[] = 'regex:/\d+\.\d{2}$/';

        if (!empty($data['min']) && $data['type'] != 'date') $valueRules[] = 'min:' . $data['min'];

        $lease = Lease::withTrashed()->find($data['lease']);

        if (!empty($data['name']) && $data['name'] == 'start_date')   $valueRules[] = "before_or_equal:".$lease->end_date;
        if (!empty($data['name']) && $data['name'] == 'end_date')   $valueRules[] = "after_or_equal:".$lease->start_date;

        $rules = [
            'value' => $valueRules
        ];

        return [];
    }

    public function attributes()
    {
        $data = $this->request->all();
        return [
            'value' => preg_replace(
                '/\_/',
                " ",
                !empty($data['name']) ? $data['name'] : ''
            )
        ];
    }

    public function messages()
    {
        $messages = [
            'value.before_or_equal' => "The start date must be a date before or equal to end date",
            'value.after_or_equal' => "The end date must be a date after or equal to start date"
        ];

        $data = $this->request->all();
        if ($data['type'] == 'numeric') $messages['value.regex'] = "The field must be in the format 123.45";

        return $messages;
    }
}
