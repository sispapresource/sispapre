<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HallazgoFile extends Model
{

    protected $table = 'hallazgos_files';

    protected $fillable = [
        'url', 'id_hallazgo', 'name_user',
    ];

}
