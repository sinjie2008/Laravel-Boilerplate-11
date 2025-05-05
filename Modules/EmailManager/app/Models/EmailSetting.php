<?php

namespace Modules\EmailManager\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    use HasFactory;

    protected $table = 'email_settings'; // Explicitly define table name

    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password', // Consider encrypting this field
        'encryption',
        'from_address',
        'from_name',
    ];

    // Optional: Add casting for encrypted password if implemented
    // protected $casts = [
    //     'password' => 'encrypted',
    // ];

    // Optional: Add factory if needed for testing
    // protected static function newFactory()
    // {
    //     return \Modules\EmailManager\Database\factories\EmailSettingFactory::new();
    // }
}
