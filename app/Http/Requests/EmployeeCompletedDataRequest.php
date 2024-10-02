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
            'name'=>'nullable|string',
            'company_image'=>'nullable|file|mimes:jpeg,png,jpg,gif',
            'company_name' => 'nullable',
            'instagram_link'=>'nullable',
            'linked_in_link'=>'nullable',
            'description' => 'required|string',
            'imageSSN' => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'livePhoto' => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'nationalId' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:13',
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'section_id' => 'required|exists:sections,id',
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
            'zipCode' => 'nullable|string|max:255',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
        ];
    }
}
