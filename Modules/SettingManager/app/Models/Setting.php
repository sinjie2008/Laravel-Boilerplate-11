<?php

namespace Modules\SettingManager\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    // If you plan to use factories, you would define the factory relationship here.
    // For now, since the factory wasn't created automatically, I'll comment it out.
    // protected static function newFactory(): SettingFactory
    // {
    //     return SettingFactory::new();
    // }
}
