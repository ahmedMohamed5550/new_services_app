<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'desc' => 'required|string',
            'location' => 'required|string',
            'imageSSN' => 'file|mimes:jpeg,png,jpg,gif',
            'livePhoto' => 'file|mimes:jpeg,png,jpg,gif',
            'nationalId' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:13',
            'min_price' => 'required',
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'works' => 'nullable|array|max:4',
            'works.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ];
    }
}
