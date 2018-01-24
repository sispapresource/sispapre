<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemFile extends Model
{

    protected $table = 'items_files';

    protected $fillable = [
        'url', 'id_item', 'name_user','name_file'
    ];

}
