<?php

namespace Modules\LatexManager\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LatexManagerSetting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'latex_manager_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pdflatex_path',
    ];

    // Define factory if needed later
    // protected static function newFactory(): YourFactoryClass
    // {
    //     //return YourFactoryClass::new();
    // }
} 