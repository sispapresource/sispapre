<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\VersionPropuesta;
use App\CategoriaPropuesta;
use App\ItemPropuesta;
use App\UnidadItem;

class DetalleVersionPropuesta extends Model
{
    //
    protected $guarded=[];
    protected $table = 'detalles_version_propuesta';

    protected $primaryKey = 'id';
    public $timestamps = true;

    public function version(){
        return $this->belongsTo(VersionPropuesta::class,'id_version');
    }

    public function categoria(){
        return $this->belongsTo(CategoriaPropuesta::class,'id_categoria');
    }

    public function item(){
        return $this->belongsTo(ItemPropuesta::class,'propuesta_items');
    }

    public function unidad(){
        return $this->belongsTo(UnidadItem::class,'id_unidad');
    }

    public function total(){
        return $this->cantidad * $this->precio_unitario;
    }


}
