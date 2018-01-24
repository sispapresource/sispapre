<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Documento;
class TipoProyecto extends Model
{
    //
    protected $guarded=[];
    protected $table = 'tipoProyecto';

    protected $primaryKey = 'id';

    public $timestamps = false;
    
    public function documentos(){
        return $this->belongsToMany(Documento::class);
    }
    
    
}
