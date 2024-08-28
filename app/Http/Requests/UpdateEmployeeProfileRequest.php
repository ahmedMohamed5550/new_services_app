<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update this based on your authorization logic
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'desc' => 'sometimes|string',
            'min_price' => 'sometimes|numeric',
        ];
    }
}
