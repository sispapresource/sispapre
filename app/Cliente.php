<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Proupuesta;
class Cliente extends Model
{
    //
    protected $table = 'clientes';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded=[];

    public function propuestas(){
        return $this->belongsToMany(Propuesta::class,'clientes_propuestas')->withTimestamps();
    }

}
