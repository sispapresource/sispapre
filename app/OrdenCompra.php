<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cuenta;
class OrdenCompra extends Model
{
    protected $table = 'ordenes_de_compra';

    public function cuenta(){
        return $this->belongsTo(Cuenta::class,'id_cuenta');
    }
}
