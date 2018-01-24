<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inspeccion extends Model
{
    protected $table = 'inspecciones';

    public $timestamps = false;
    protected $guarded = [
    ];
    
}