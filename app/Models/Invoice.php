<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'bank_id',
        'currency',
        'invoice_date',
        'due_date',
        'quantity_text',
        'rate_text',
        'tax_text',
        'amount_text',
        'sub_total',
        'total_amount',
        'customer_note',
        'status',
        'invoice_number',
        'created_by'
    ];

    public function invoiceServices()
    {
        return $this->hasMany(InvoiceService::class);
    }
    public function bankingDetail()
    {
        return $this->belongsTo(BankingDetail::class, 'bank_id', 'id');
    }
    public function customerDetail()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'invoice_id');
    }
}
