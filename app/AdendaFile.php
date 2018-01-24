<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdendaFile extends Model
{

    protected $table = 'adendas_files';

    protected $fillable = [
        'url', 'id_adenda', 'name_user','name_file'
    ];

}
