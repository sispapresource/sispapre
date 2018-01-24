<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Ajuste;
use App\Item;
use App\CentroContable;

class Adenda extends Model
{
    protected $table = 'adendas';

    public $timestamps = false;

    protected $appends  = ['centro','costo' ,'utilidad' ,'admin','itbms','subtotal','total','fechadoc'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function ajustes(){
        return $this->hasMany(Ajuste::class,'id_adenda');
    }

    public function items()
    {
        return $this->hasManyThrough(
            Item::class,Ajuste::class,
            'id_adenda','id_ajuste','id'
        );
    }

    public function centroContable(){
        return $this->hasMany(CentroContable::class,'id_centro');
    }

    public function getCentroAttribute(){
        return CentroContable::find($this->id_centro)->nombre_centro;
    }

    public function getCostoAttribute()
    {
        return Adenda::find($this->id)->items->sum('monto');
    }

    public function getUtilidadAttribute(){
        return Adenda::find($this->id)->ajustes->sum('utilidad');
    }

    public function getAdminAttribute(){
        return Adenda::find($this->id)->ajustes->sum('administracion');
    }

    public function getItbmsAttribute(){
        return Adenda::find($this->id)->ajustes->sum('itbms');
    }

    public function getSubTotalAttribute(){
        return $this->costo + $this->utilidad + $this->admin;
    }
    public function getTotalAttribute(){
        return $this->subtotal+$this->itbms;
    }

    public function getFechaDocAttribute()
    {
        return \Carbon\Carbon::parse($this->fecha)->format('d/m/Y');
    }
}