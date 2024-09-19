<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'comment' => 'nullable|string',
            'rating' => 'required|in:1,2,3,4,5',
            'user_id' => 'required|integer|exists:users,id',
            'employee_id' => 'required|integer|exists:users,id',
        ];
    }
}
