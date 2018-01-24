<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\EstadoPropuesta;
use App\Propuesta;
use App\User;
class VersionPropuesta extends Model
{
    //
    protected $guarded=[];
    protected $table = 'versiones_propuesta';

    protected $primaryKey = 'id';
    public $timestamps = true;

    public function propuesta(){
        return $this->belongsTo(Propuesta::class,'id_propuesta');
    }

    public function usuario(){
        return $this->belongsTo(User::class,'id_usuario');
    }

    public function estado(){
        return $this->belongsTo(EstadoPropuesta::class,'id_estado');
    }
    public function montoTotal(){
        $detalles = DetalleVersionPropuesta::where('id_version',$this->id)->get();
        $total = 0;
        foreach($detalles as $detalle){
            $total+=$detalle->total();
        }
        return $total;
            
    }

}
