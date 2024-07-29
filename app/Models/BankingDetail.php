<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'branch_code',
        'account_title',
        'iban_number'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'bank_id', 'id');
    }
}
