<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest as BaseRequest;

class UpdateUserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'name' => 'required|string|unique:users,name',
            'email' => 'required|email|unique:users,email,' . $this->input('user_id'),
            'company' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|regex:/^[0-9+\(\)#\.\s\/ext-]+$/',
        ];
    }
}
