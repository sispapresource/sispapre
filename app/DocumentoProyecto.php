<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CentroContable;
use App\User;
use App\Documento;

class DocumentoProyecto extends Model
{
    //
    protected $guarded=[];
    protected $table = 'centro_contable_documento';

    protected $primaryKey = 'id';

    public $timestamps = false;
//
//    public function proyecto(){
//        return $this->belongsToMany(CentroContable::class);
//    }
//
//    public function usuario(){
//        return $this->belongsToMany(User::class);
//    }
//
//    public function documento(){
//        return $this->belongsToMany(User::Documento);
//    }

}
