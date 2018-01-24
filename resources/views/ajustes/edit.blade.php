@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Editar Ajuste {{ $nro_ajuste }} a adenda - Adenda {{ $nro_adenda }} - {{ $nombre_centro }} </h5>
                    </div>
                    <input type="hidden" id="idAjuste" class="form-control" value="{{ $id_ajuste }}">
                    {{ csrf_field() }}
                    <div class="ibox-content">
                        <div class="row">
                        	<div class="form-group col-sm-4">
                                <label for="nro_ajuste" class="btn-block">Número de ajuste</label>
                                <input class="form-control" type="text" value="{{ $nro_ajuste }}" class="form-control" id="nro_ajuste">
                            </div>      
                            <div class="form-group col-sm-4" id="data_1">
                                <label for="inputActualizacion" class="btn-block">Fecha del ajuste</label>
                                <div class="input-group date" id="inputActualizacion" >
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="date_ajuste" class="form-control" value="{{ $fecha }}" type="text">
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-sm-4 form-group"> 
                                <label for="inputCuenta" class="btn-block">Utilidad</label>
                                <div class="input-group date" id="inputActualizacion" >
                                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                    <input class="form-control amountNew" type="text" id="utilidad" value="{{ $utilidad }}">
                                </div>  
                            </div>       
                            <div class="col-sm-4 form-group"> 
                                <label for="inputCuenta" class="btn-block">Administración</label>
                                <div class="input-group date" id="inputActualizacion" >
                                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                    <input class="form-control amountNew" type="text" id="administracion" value="{{ $administracion }}">
                                </div>  
                            </div> 
                            <div class="col-sm-4 form-group"> 
                                <label for="inputCuenta" class="btn-block">ITBMS</label>
                                <div class="input-group date" id="inputActualizacion" >
                                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                    <input class="form-control amountNew" type="text" id="itbms" value="{{ $itbms }}">
                                </div>  
                            </div> 
                            <div class="col-sm-12 form-group"> 
                                <label for="descripcion_adenda" class="btn-block">Descripción</label>
                                <input type="text" value="{{ $descripcion }}" id="descripcion_ajuste" class="form-control">
                            </div> 
                        </div>
                        <div class="row" id="div-button-save">       
                            <div class="form-group col-sm-3 col-sm-offset-9">
                                <button type="button" id="button-edit-ajuste" class="btn-primary btn-lg btn btn-block" onclick="edit_ajuste({{ $id_ajuste }})">Guardar</button>
                            </div>
                        </div>
                    </div>   
                </div>                          
            </div>
        </div>
    </div>
</div>

@endsection

