<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CentroUser extends Model
{
    protected $table = 'centro_contable_user';

        protected $fillable = [
         'id', 'centro_contable_id','user_id',
    ];
}