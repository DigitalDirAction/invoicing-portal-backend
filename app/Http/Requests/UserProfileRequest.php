<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
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
            'user_id' => 'required',
            'email' => 'email|unique:users,email,' . $this->input('user_id') . ',id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'language' => 'required|string',
            'gender' => 'required|string',
            'profile_image' => [
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048', // Maximum file size in kilobytes
            ],
        ];
    }
}
