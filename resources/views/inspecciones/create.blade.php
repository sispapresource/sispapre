@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Crear Hallazgo - Proyecto {{ $centro->nombre_centro }} </h5>
                    </div>
                    <div class="ibox-content">
                        <form role="form" method="POST" action="{{ url('/guardarevaluacion',[$centro->id_centro]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label for="nro_ajuste" class="btn-block">No. de hallazgo</label>
                                    <input class="form-control" type="text" class="form-control" name="numero">
                                </div>      
                                <div class="col-sm-3 form-group"> 
                                    <label for="descripcion_adenda" class="btn-block">Referencia</label>
                                    <input type="text" placeholder="Escriba una referencia" name="referencia" class="form-control">
                                </div> 
                                <div class="col-sm-3 form-group"> 
                                    <label for="descripcion_adenda" class="btn-block">Encargado por</label>
                                    <input type="text" placeholder="Escriba una descripción" name="encargado" class="form-control">
                                </div> 
                                <div class="form-group col-sm-3" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Fecha</label>
                                    <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input name="fecha" class="form-control" placeholder="Ingrese la Fecha" type="text">
                                    </div>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="nro_ajuste" class="btn-block">Puntaje</label>
                                    <input class="form-control" class="form-control" name="puntaje" type="number" min="0" max="100">
                                </div>      
                            </div>
                            <div class="col-md-10 col-md-offset-1">
                                @include('layouts.errors')
                            </div>
                            <div class="modal-header">
                                <h3><span>Importación de la evaluación</span></h3>
                            </div>
                            <div class="modal-body">
                                <div class="upload-div">
                                    <input name="documento" type="file" class="btn btn-gray"/>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="col-sm-offset-6 col-sm-3">
                                    <a class="btn btn-gray btn-block" href="{!! url('home_seguridad') !!}" style="color:white;">Cancelar</a>
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-primary btn-block">Guardar</button>
                                </div>
                            </div>
                        </form>
                    </div>   
                </div>                          
            </div>
        </div>
    </div>
</div>

@endsection

