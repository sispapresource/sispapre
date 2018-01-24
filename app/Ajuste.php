<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Item;
class Ajuste extends Model
{
    protected $table = 'ajustes';

    public $timestamps = false;

    public function items()
    {
        return $this->hasMany(Item::class,'id_ajuste');
    }

}