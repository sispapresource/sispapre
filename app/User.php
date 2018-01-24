<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Bican\Roles\Traits\HasRoleAndPermission;
use Bican\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasRoleAndPermissionContract {

    use Authenticatable, CanResetPassword, HasRoleAndPermission;

    protected $fillable = [
        "name", "email", "password",
    ];

    protected $hidden = [
        "password", "remember_token",
    ];

    public function presentations () {

        return $this->hasMany( "App/Presentation" );

    }

    public function centros (){
        return $this->belongsToMany('App\CentroContable', 'centro_contable_user', 'user_id', 'centro_contable_id');
    }

}