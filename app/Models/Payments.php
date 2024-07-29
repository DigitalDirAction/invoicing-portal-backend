<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id',
        'invoice_number',
        'customer_name',
        'payment_number',
        'amount_received',
        'amount_due',
        'payment_date',
        'payment_method',
        'reference',
        'receipt',
        'created_by',
        'updated_by'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function invoiceReceived()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
