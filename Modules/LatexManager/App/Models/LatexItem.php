<?php

namespace Modules\LatexManager\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\LatexManager\Database\factories\LatexItemFactory;

class LatexItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'content',
        'latex_editor', // Changed from textarea
    ];
    
    protected static function newFactory(): LatexItemFactory
    {
        //return LatexItemFactory::new();
    }
}
