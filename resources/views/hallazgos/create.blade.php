@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Crear Hallazgo - Proyecto {{ $nombre_centro }} </h5>
                    </div>
                    {{ csrf_field() }}
                    <div class="ibox-content">
                        <form role="form" method="POST" action="{{ url('/hallazgo_guardar') }}" enctype="multipart/form-data">
                            <input type="hidden" id="idCentro" name="idCentro" class="form-control" value="{{ $id_centro }}">
                            <div class="row">
                            	<div class="form-group col-sm-3">
                                    <label for="nro_ajuste" class="btn-block">No. de hallazgo</label>
                                    <input class="form-control" type="text" value="{{ $ultimo_registro }}" class="form-control" id="nro_hallazgo" name="nro_hallazgo">
                                </div>      
                                <div class="col-sm-3 form-group"> 
                                    <label for="descripcion_adenda" class="btn-block">Referencia</label>
                                    <input type="text" placeholder="Escriba una referencia" id="referencia_hallazgo" name="referencia_hallazgo" class="form-control">
                                </div> 
                                <div class="col-sm-3 form-group"> 
                                    <label for="descripcion_adenda" class="btn-block">Inspeccionado por</label>
                                    <input type="text" placeholder="Escriba una descripción" id="encargado_hallazgo" name="encargado_hallazgo" class="form-control">
                                </div> 
                                <div class="form-group col-sm-3" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Fecha del hallazgo</label>
                                    <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="date_hallazgo" name="date_hallazgo" class="form-control" placeholder="Ingrese la Fecha" type="text">
                                    </div>
                                </div>
                            </div>
                            {{ csrf_field() }}
                            <div class="modal-header">
                                <h3><span>Importación de Inspección</span></h3>
                            </div>
                            <div class="modal-body">
                                <div class="upload-div">
                                    <input name="document" type="file" class="btn btn-gray"/>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="col-sm-offset-6 col-sm-3">
                                    <a class="btn btn-gray btn-block" href="{!! route('home_hallazgos', ['idCentro'=>$id_centro]) !!}" style="color:white;">Cancelar</a>
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

