<?php

namespace Modules\ExcelManager\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ExcelManager\Database\factories\ItemFactory;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'price',
        'description',
        'quantity'
    ];
    
    protected static function newFactory()
    {
        return ItemFactory::new();
    }
}
