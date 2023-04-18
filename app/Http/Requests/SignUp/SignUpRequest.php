<?php

namespace App\Http\Requests\SignUp;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_name' => 'required',
            'phone_number' => 'required|unique:users,phone_number|regex:/^\+?3?8?(0\d{9})$/',
        ];
    }

    public function messages()
    {
        return [
            'phone_number.regex' => 'The phone number field format is invalid for Ukrainian mobile operators.',
            'phone_number.unique' => 'This phone number is already taken.',
        ];
    }
}
