<?php

namespace App\Http\Requests\BankingDetail;

use App\Http\Requests\BaseRequest as BaseRequest;

class UpdateBankRequest extends BaseRequest
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
        $bankID = $this->route('bankID');
        return
            [
                'bank_name' => 'required',
                'branch_code' => 'required',
                'account_title' => 'required',
                'iban_number' => 'required|unique:banking_details,iban_number,' . $bankID,

            ];
    }
}

