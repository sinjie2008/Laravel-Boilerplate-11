<?php

namespace Modules\ServiceApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes; // Added
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Added
use App\Models\User; // Assuming standard User model path
// use Modules\ServiceApi\Database\Factories\ServiceFactory;

class Service extends Model
{
    use HasFactory, SoftDeletes; // Added SoftDeletes

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    // protected static function newFactory(): ServiceFactory
    // {
    //     // return ServiceFactory::new();
    // }

    /**
     * Get the user that owns the service.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
