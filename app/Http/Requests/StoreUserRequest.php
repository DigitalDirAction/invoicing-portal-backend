<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest as BaseRequest;

class StoreUserRequest extends BaseRequest
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'company' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|regex:/^[0-9+\(\)#\.\s\/ext-]+$/',
        ];
    }
}
