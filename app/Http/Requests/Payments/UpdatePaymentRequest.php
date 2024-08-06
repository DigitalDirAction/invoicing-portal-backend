<?php

namespace App\Http\Requests\Payments;

use App\Http\Requests\BaseRequest as BaseRequest;

class UpdatePaymentRequest extends BaseRequest
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
            'invoice_number' => 'required',
            'customer_name' => 'required',
            'payment_number' => 'required',
            'amount_received' => 'required',
            'amount_due' => 'required',
            'payment_date' => 'required',
            'payment_method' => 'required',
            'reference' => 'required',
            'receipt' => '',
        ];
    }
}
