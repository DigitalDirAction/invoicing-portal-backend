<?php

namespace App\Http\Requests\Invoices;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
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
        return
            [
                'customer_id' => 'required',
                'bank_id' => 'required',
                'currency' => 'required',
                'invoice_date' => 'required',
                'due_date' => 'required',
                'quantity_text' => 'required',
                'rate_text' => 'required',
                'tax_text' => 'required',
                'amount_text' => 'required',
                'sub_total' => 'required',
                'total_amount' => 'required',
                'customer_note' => 'required',
                'status' => 'required',
                'service_description' => 'sometimes|array',
                'quantity' => 'sometimes|array',
                'rate' => 'sometimes|array',
                'tax' => 'sometimes|array',
                'amount' => 'sometimes|array'

            ];
    }
    public function setInvoiceData(): array
    {
        $invoice = $this->validated();

        return [
            'customer_id' => $invoice['customer_id'],
            'bank_id' => $invoice['bank_id'],
            'currency' => $invoice['currency'],
            'invoice_date' => $invoice['invoice_date'],
            'due_date' => $invoice['due_date'],
            'quantity_text' => $invoice['quantity_text'],
            'rate_text' => $invoice['rate_text'],
            'tax_text' => $invoice['tax_text'],
            'amount_text' => $invoice['amount_text'],
            'sub_total' => $invoice['sub_total'],
            'total_amount' => $invoice['total_amount'],
            'customer_note' => $invoice['customer_note'],
            'status' => $invoice['status'],
        ];
    }
    public function setInvoiceServiceData(): array
    {
        $invoice = $this->validated();

        return [
            'service_description' => $invoice['service_description'],
            'quantity' => $invoice['quantity'],
            'rate' => $invoice['rate'],
            'tax' => $invoice['tax'],
            'amount' => $invoice['amount'],
        ];
    }
}
