<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PermisoRol extends Model
{
    protected $table = 'permission_role';

        protected $fillable = [
         'id', 'permission_id','role_id',
    ];
}