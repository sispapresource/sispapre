<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $table = 'cuentas';

    protected $primaryKey = 'id_cuenta';

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];
}
