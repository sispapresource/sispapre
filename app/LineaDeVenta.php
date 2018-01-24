<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LineaDeVenta extends Model
{
    //
    
    public $table = 'propuesta_lineas_de_venta';

    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded=[];


}
