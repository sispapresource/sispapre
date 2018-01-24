<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ValorConfiguraciones;
class ConfiguracionFormula extends Model
{
    protected $table = 'configuracion_formulas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
    ];

    //protected $primaryKey = ['id_cuenta', 'id_centro_contable'];

    public $timestamps = false;

    public function opciones(){
        return $this->hasMany(ValorConfiguraciones::class, 'id_configuracion');
    }
    
}
