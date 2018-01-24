<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogLogin extends Model
{
    protected $table = 'login';

    protected $fillable = [
        'id_usuario', 'fecha'
    ];

    public $timestamps = false;

}