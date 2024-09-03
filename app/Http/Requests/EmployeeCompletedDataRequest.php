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
            'description' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'section_id' => 'required|exists:sections,id',
            'phone_number_1'=>'nullable|string',
            'phone_number_2'=>'nullable|string',
            'mobile_number_1'=>'nullable|string',
            'mobile_number_2'=>'nullable|string',
            'fax_number'=>'nullable',
            'whatsapp_number'=>'nullable',
            'facebook_link'=>'nullable',
            'website'=>'nullable',
            'city' => 'nullable|string|max:255',
            'bitTitle' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'specialMarque' => 'nullable|string|max:255',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'works' => 'nullable|array|max:4',
            'works.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ];
    }
}
