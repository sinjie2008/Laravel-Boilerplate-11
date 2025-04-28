<?php

namespace Modules\ExcelImportExcel\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ExcelImportExcel\Database\factories\ItemFactory;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    
    protected static function newFactory(): ItemFactory
    {
        //return ItemFactory::new();
    }
}
