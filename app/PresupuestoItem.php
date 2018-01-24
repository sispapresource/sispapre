<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cuenta;
use App\Item;
use App\UnidadItem;
class PresupuestoItem extends Model
{
    //
    protected $table = 'presupuesto_items';
    
    public $timestamps = false;
    
    protected $appends = array('total');

    protected $guarded = [];
    
    public function cuentas(){
        return $this->hasOne(Cuenta::class, 'id_cuenta','id_cuenta');
    }
    
    public function getTotalAttribute()
    {
        return $this->cantidad * $this->PrecioUnitario;  
    }
    
    public function Total(){
        return $this->cantidad * $this->PrecioUnitario;  
    }
    
    public function item(){
        return $this->hasOne(ItemPropuesta::class,'id','id_propuesta_items');
    }

    public function unidad(){
        return $this->hasOne(UnidadItem::class,'id','id_unidad');
    }
    
}
