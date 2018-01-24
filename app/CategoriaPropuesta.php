<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\DetalleVersionPropuesta;
class CategoriaPropuesta extends Model
{
    //
    protected $guarded=[];
    protected $table = 'propuesta_categorias';

    protected $primaryKey = 'id';
    public $timestamps = false;
    
     public function detalles(){
        return $this->hasMany(DetalleVersionPropuesta::class,'id_categoria');
    }
}
