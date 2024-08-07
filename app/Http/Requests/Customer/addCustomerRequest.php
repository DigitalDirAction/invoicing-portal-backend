<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\BaseRequest as BaseRequest;

class addCustomerRequest extends BaseRequest
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
            'customer_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'company_name' => 'required',
            'currency' => 'required',
            'email' => 'email|unique:customers,email',
            'phone_number' => '',
            'mobile_number' => 'required',
            'address' => 'required',
            'logo' => '',
        ];
    }
}
