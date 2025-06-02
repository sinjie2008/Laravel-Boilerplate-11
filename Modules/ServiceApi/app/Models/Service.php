<?php

namespace Modules\ServiceApi\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'description', 'user_id'];

    protected $table = 'services'; // Ensure this matches your migration table name
}
