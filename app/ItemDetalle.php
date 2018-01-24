<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemDetalle extends Model
{
    protected $table = 'items_detalle';

    protected $fillable = ['id_item', 'id_cuenta', 'monto_anterior', 'monto_nuevo'];

    public $timestamps = false;

}