<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cuenta;
class PresupuestoAvance extends Model
{
    protected $table = 'presupuesto_y_avance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'id_cuenta', 'id_centro_contable','presupuesto','porcentaje_avance','porcentaje_teorico'
    ];

    //protected $primaryKey = ['id_cuenta', 'id_centro_contable'];

    public $timestamps = false;

    public function cuenta(){
        return $this->belongsTo(Cuenta::class, 'id_cuenta');
    }
    
}
