<?php

namespace Modules\FundRequest\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'purpose',
        'status',
        'admin_id',
        'super_admin_id',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id');
    }

    public function superAdmin()
    {
        return $this->belongsTo(\App\Models\User::class, 'super_admin_id');
    }
}
