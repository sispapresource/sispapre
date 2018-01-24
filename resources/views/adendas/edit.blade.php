@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Editar Adenda al Presupuesto - &nbsp;<h5 id="proyecto-title"> {{ $nombre_centro }} </h5></h5>
                    </div>
                    <input type="hidden" id="idAdenda" class="form-control" value="{{ $id_adenda }}">
                    <input type="hidden" id="estadoSel" class="form-control" value="{{ $estado }}">
                    {{ csrf_field() }}
                    <div class="ibox-content">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="nro_adenda" class="btn-block">Proyecto</label>
                                <input class="form-control" type="text" class="form-control" value="{{ $nombre_centro }}" disabled>
                            </div>    
                            <div class="form-group col-sm-4" id="data_1">
                                <label for="inputActualizacion" class="btn-block">Fecha del documento</label>
                                <div class="input-group date" id="inputActualizacion" >
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="date_adenda" class="form-control" value="{{ $fecha }}" type="text">
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="nro_adenda" class="btn-block">Número de documento</label>
                                <input class="form-control" type="text" value="{{ $numero }}" class="form-control" id="nro_adenda">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4" id="div-select-proyectos">
                                <label for="inputCuenta" class="btn-block">Estado de la adenda</label>
                                <select name="select" class="form-control" id="select-estados" required>
                                    <option value="" disabled selected hidden>Seleccione el estado</option>
                                    @include('partials/adendas/select-estado')
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group"> 
                                <label for="descripcion_adenda" class="btn-block">Descripción</label>
                                <input type="text" value="{{ $descripcion }}" id="descripcion_adenda" class="form-control">
                            </div> 
                        </div>
                        <div class="row" id="div-button-save">       
                            <div class="form-group col-sm-3 col-sm-offset-9">
                                <button type="button" id="button-save" class="btn-primary btn-lg btn btn-block" onclick="save({{ $id_adenda }})">Guardar</button>
                            </div>
                        </div>
                    </div>   
                </div>                          
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
    <script src="js/adenda_editar.js"></script>
@stop

