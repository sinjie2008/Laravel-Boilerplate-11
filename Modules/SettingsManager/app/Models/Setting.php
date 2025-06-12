<?php

namespace Modules\SettingsManager\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\SettingsManager\Database\Factories\SettingFactory;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'string', // Default to string, handle specific types in accessors/mutators or logic
    ];

    /**
     * Get the factory for the model.
     */
    protected static function newFactory(): SettingFactory
    {
        return SettingFactory::new();
    }

    /**
     * Get the value of the setting, cast to its appropriate type.
     *
     * @param  string  $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        return match ($this->type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Set the value of the setting, converting to string if necessary.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = is_array($value) ? json_encode($value) : (string) $value;
    }
}
