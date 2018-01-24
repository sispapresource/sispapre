<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ValorConfiguraciones extends Model
{
    //
    protected $table = 'valor_configuraciones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
    ];

    //protected $primaryKey = ['id_cuenta', 'id_centro_contable'];

    public $timestamps = false;
}
