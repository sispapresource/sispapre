<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Inspeccion;
use App\ItemDetalle;
use App\Hallazgo;
use DB;
class CentroContable extends Model
{
    
    protected $table = 'centros_contables';
    
    protected $fillable = [
        'id_centro','nombre_centro','contratante', 'tel_contratante','monto_contratado','tipo',
    ];
    
    protected $primaryKey = 'id_centro';
    
    public $timestamps = false;
    
    public function estado(){
        return $this->belongsTo(Estado::class);
    }
    
    public function tipoProyecto(){
        return $this->belongsTo(TipoProyecto::class,'tipo');
    }
    
    public function documentos(){
        return $this->belongsToMany(Documento::class)->withPivot('user_id','url','fecha_de_carga','estado');
    }
    
    public function inspeccion(){
        return $this->hasMany(Inspeccion::class,'id_centro');
    }
    
    public function ultimaInspeccion(){
        if($this->inspeccion){
            return $this->inspeccion->sortByDesc('fecha')->first();        
        }
        return null;
    }
    
    public function hallazgos ()    
    {
        return $this->hasMany(Hallazgo::class,'id_centro');
    }
    
    public function ordenesCompra(){
        return $this->hasMany(OrdenCompra::class,'id_centro');   
    }
    
    public function facturas(){
        return $this->hasMany(Factura::class,'id_centro');
    }
    
    public function planillas(){
        return $this->hasMany(Planilla::class,'id_centro');
    }

    public function adendas(){
        return $this->hasMany(Adenda::class,'id_centro');
    }

    public function items(){
        return ItemDetalle
        ::join('items', 'items.id', '=', 'items_detalle.id_item')
        ->join('ajustes', 'ajustes.id', '=', 'items.id_ajuste')
        ->join('adendas', 'adendas.id', '=', 'ajustes.id_adenda')
        ->where('adendas.id_centro', '=', $this->id_centro)
        ->where('adendas.estado', '=', 'aprobado')        
        ->select('ajustes.fecha','items_detalle.id_cuenta',DB::raw('sum(items_detalle.monto_nuevo) as monto_nuevo'))
        ->groupBy('items_detalle.id_cuenta');
    }


    public function montoAdendas(){
        return DB::table('items_detalle')
        ->join('items', 'items.id', '=', 'items_detalle.id_item')
        ->join('ajustes', 'ajustes.id', '=', 'items.id_ajuste')
        ->join('adendas', 'adendas.id', '=', 'ajustes.id_adenda')
        ->where('adendas.id_centro', '=', $this->id_centro)
        ->where('adendas.estado', '=', 'aprobado')
        ->sum('monto_nuevo');
    }

    public function presupuesto(){
        return DB::table('presupuesto_y_avance')->where("id_centro_contable",$this->id_centro)->sum('presupuesto');
    }

    public function gastado(){
        return Factura::where('id_centro', $this->id_centro)->sum('monto') + Planilla::where('id_centro', $this->id_centro)->sum('monto');
    }

}
