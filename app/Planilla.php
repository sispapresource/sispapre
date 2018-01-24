<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cuenta;
use App\CentroContable;
class Planilla extends Model
{
    protected $table = 'planilla';
    
    public function cuenta(){
        return $this->belongsTo(Cuenta::class,'id_cuenta');
    }
    
    public function centroContable(){
        return $this->belongsTo(CentroContable::class,'id_centro');   
        
    }
    
    public function item(){
        return $this->hasOne(ItemPropuesta::class,'nombre','desc_transaccion');
    }
}
