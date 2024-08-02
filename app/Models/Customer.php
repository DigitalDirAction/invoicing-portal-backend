<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_type',
        'first_name',
        'last_name',
        'company_name',
        'currency',
        'email',
        'phone_number',
        'mobile_number',
        'address',
        'created_by',
        'updated_by',
        'logo'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
