<?php

namespace Modules\SqlGenerator\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\SqlGenerator\Database\factories\SqlGeneratorSettingFactory;

class SqlGeneratorSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'api_url',
        'api_key',
    ];

    protected static function newFactory(): SqlGeneratorSettingFactory
    {
        //return SqlGeneratorSettingFactory::new();
    }
}
