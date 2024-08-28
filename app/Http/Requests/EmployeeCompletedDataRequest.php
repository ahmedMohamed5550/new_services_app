<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeCompletedDataRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'desc' => 'required|string',
            'location' => 'required|string',
            'imageSSN' => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'livePhoto' => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'nationalId' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:13',
            'min_price' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'works' => 'nullable|array|max:4',
            'works.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ];
    }
}
