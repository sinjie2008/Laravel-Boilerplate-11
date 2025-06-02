<?php

namespace Modules\SubscriptionManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission;

// use Modules\SubscriptionManager\Database\Factories\PlanFactory;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'price', 'api_call_limit_per_day'];

    // protected static function newFactory(): PlanFactory
    // {
    //     // return PlanFactory::new();
    // }

    /**
     * The permissions that belong to the plan.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'plan_permission');
    }
}
