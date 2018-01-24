<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemPropuesta extends Model
{
    //
    protected $guarded=[];
    protected $table = 'propuesta_items';

    protected $primaryKey = 'id';
    public $timestamps = false;
}
