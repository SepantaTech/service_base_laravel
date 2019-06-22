<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegisteredApp extends Model
{
    protected $table = 'apps';
    
    protected $casts = [
        'allowed_ips' => 'array',
    ];
}
