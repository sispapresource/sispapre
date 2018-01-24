<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ItemDetalle;
class Item extends Model
{
    protected $table = 'items';

    public $timestamps = false;

    protected $primaryKey = 'id';
    
    
    public function detalle(){
        return $this->hasMany(ItemDetalle::class,'id_item');
    }

}