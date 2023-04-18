<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditUserRequest extends FormRequest
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
        $data = $this->all();
        $userId = $data['id'];
        $phoneNumber = $data['phone_number'];

        $user = User::findOrFail($userId);

        return [
            'id' => 'required|exists:users',
            'role' => 'required|string',
            'name' => 'required|string',
            'is_active' => 'required',
            'phone_number' => [
                'required',
                'regex:/^\+?3?8?(0\d{9})$/',
                Rule::unique('users')->where(function ($query) use ($user, $phoneNumber) {
                    return $query->where('phone_number', $phoneNumber)
                        ->where('id', '<>', $user->id);
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'phone_number.regex' => 'The phone number field format is invalid for Ukrainian mobile operators. Ex: +380937775566',
            'phone_number.unique' => 'This phone number is already taken.',
        ];
    }
}
