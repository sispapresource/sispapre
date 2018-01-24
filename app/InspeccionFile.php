<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InspeccionFile extends Model
{

    protected $table = 'inspecciones_files';

    protected $fillable = [
        'url', 'id_inspeccion', 'name_user',
    ];

}
