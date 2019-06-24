<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegisteredApp extends Model
{
    protected $table = 'apps';
    
    protected $casts = [
        'allowed_ips' => 'array',
        'id' => 'string',
    ];

    public function getIdAttribute(){
        if(isset($this->attributes['id'])){
            return $this->attributes['id'];
        }
        return null;
    }

    public $incrementing = false;
}
