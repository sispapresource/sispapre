<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TipoProyecto;
use App\User;

class Documento extends Model
{
    //
    protected $guarded=[];
    protected $table = 'documentos';

    protected $primaryKey = 'id';
    public $timestamps = false;

    public function tipoproyecto(){
        return $this->belongsToMany(TipoProyecto::class);
    }

    public function proyecto(){
        return $this->belongsToMany(CentroContable::class)->withPivot('user_id','url','fecha_de_carga','estado');
    }
    public function usuario(){
        return $this->belongsToMany(User::class,'centro_contable_documento')->withPivot('url','fecha_de_carga','estado');
    }
}
