<?php

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Billing\Database\Factories\InvoiceFactory;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'due_date',
        'stripe_id',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // protected static function newFactory(): InvoiceFactory
    // {
    //     // return InvoiceFactory::new();
    // }
}
