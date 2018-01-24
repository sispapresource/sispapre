<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoProyectoDocumento extends Model
{
    //
     protected $guarded=[];
    protected $table = 'TipoProyectos_Documentos';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
