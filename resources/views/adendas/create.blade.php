@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Crear Adenda&nbsp;<h5 id="proyecto-title"> @if (! empty($nombre_centro))al Presupuesto - {{ $nombre_centro }} </h5> @endif </h5>
                    </div>
                    <input type="hidden" id="idCentro" class="form-control" @if (! empty($id_centro)) value="{{ $id_centro }}"  @endif>
                    {{ csrf_field() }}
                    <div class="ibox-content">
                        <div class="row">
                        	<div class="form-group col-sm-4" id="div-select-proyectos">
                                <label for="inputCuenta" class="btn-block">Proyecto</label>
                                <select name="select" class="form-control select-proyectos" id="select-proyectos" required>
                                    <option value="" disabled selected hidden>Seleccione un proyecto</option>
                                </select>
                            </div>       
                            <div class="form-group col-sm-4" id="data_1">
                                <label for="inputActualizacion" class="btn-block">Fecha del documento</label>
                                <div class="input-group date" id="inputActualizacion" >
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="date_adenda" class="form-control" placeholder="Ingrese la Fecha" type="text">
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="nro_adenda" class="btn-block">Número de documento</label>
                                <input class="form-control" type="text" class="form-control" id="nro_adenda">
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
                                <input type="text" placeholder="Escriba una descripción" id="descripcion_adenda" class="form-control">
                            </div> 
                        </div>
                        <div class="row" id="div-button-save">       
                            <div class="form-group col-sm-3 col-sm-offset-9">
                                <button type="button" id="button-save" class="btn-primary btn-lg btn btn-block" onclick="save()">Guardar</button>
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
    <script src="js/adenda_crear.js"></script>
@stop

