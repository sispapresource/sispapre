<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CentroContable;
use App\LineaDeVenta;
use App\User;
use App\VersionPropuesta;
use App\Cliente;
class Propuesta extends Model
{
    //
    protected $guarded=[];
    protected $table = 'propuestas';

    protected $primaryKey = 'id';
    public $timestamps = true;

    public function cuenta(){
        return $this->belongsTo(CentroContable::class,'id_centro');
    }
    public function lineaDeVenta(){
        return $this->belongsTo(LineaDeVenta::class,'id_linea_de_venta');
    }
    public function usuario(){
        return $this->belongsTo(User::class,'id_usuario');
    }
    public function versiones(){
        $versiones = VersionPropuesta::where('id_propuesta',$this->id)->get();

        return count($versiones);
    }
    public function ultimaversion(){
        $version=VersionPropuesta::where('id_propuesta',$this->id)
            ->orderBy('created_at','DESC')
            ->first();
        return $version;
    }

    public function estado(){
        $version=VersionPropuesta::where('id_propuesta',$this->id)
            ->orderBy('created_at','DESC')
            ->first();
        if($version){

            return $version->estado->id;
        }else{
            return -1;
        }
    }

    public function clientes(){
        return $this->belongsToMany(Cliente::class,'clientes_propuestas')->withTimestamps();
    }
}
