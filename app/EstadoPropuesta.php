<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoPropuesta extends Model
{
    //
    
    protected $guarded=[];
    protected $table = 'estados_propuesta';

    protected $primaryKey = 'id';
    public $timestamps = false;
}
