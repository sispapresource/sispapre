<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnidadItem extends Model
{
    //
        protected $guarded=[];
    protected $table = 'item_unidades';

    protected $primaryKey = 'id';
    public $timestamps = false;
}
