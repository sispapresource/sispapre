@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Cargar Datos Iniciales al Sistema</h5>
                    </div>

                    <div class="ibox-content">


                        <div class="row" id="app">
                            <form role="form" method="POST" action="{{ url('/upload_file') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="idAdenda" id="idAdenda">
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="selectcentro" class="btn-block">Proyecto</label>
                                        <select name="selectcentro" class="form-control" id="selectcentro">
                                            <option value="" disabled selected hidden>Seleccione el proyecto</option>
                                            @foreach ($proyectos as $proyecto)
                                            <option value="{{ $proyecto->id_centro }}">{{ $proyecto->nombre_centro }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="selectcentro" class="btn-block">Tipo de Carga</label>
                                        <select name="selectcarga" class="form-control" id="selectcarga"> 
                                            <option value="" disabled selected hidden>Seleccione tipo de carga</option>                                           
                                            @permission('cargar.presupuesto')
                                                <option value="presupuesto">Presupuesto</option>
                                            @endpermission                         
                                            @permission('cargar.cantidades')
                                            <option value="cantidades">Presupuesto con cantidades</option>
                                            @endpermission
                                            @permission('cargar.fisico')
                                                <option value="avance">Avance Fisico</option>
                                            @endpermission
                                            @permission('cargar.teorico')
                                                <option value="teorico">Avance Teorico</option>
                                            @endpermission
                                        </select>
                                    </div>
                                </div>
                                <div class="upload-div" style="padding-left:80px;">
                                    <input name="document" type="file" class="btn btn-gray col-sm-6 col-md-offset-3 b-r"/>
                                </div>
                                <div class="modal-footer">
                                    <div class="col-sm-6 col-md-offset-3 b-r">
                                        <button type="submit" class="btn btn-primary btn-block">Guardar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if(Session::get('errores'))
                        @foreach(Session::get('errores') as $error)
                        <div class="alert alert-danger" role="alert">
                            <strong>Error:</strong> {{ $error }}
                        </div>
                        @endforeach
                        @endif

                    </div>                              
                </div>
            </div>
        </div>
    </div>
</div>

@endsection