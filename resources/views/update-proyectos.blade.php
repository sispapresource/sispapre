@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Actualizar Detalles - {{ $centro->nombre_centro }} </h5>
                    </div>
                    <input type="hidden" id="idCentro" class="form-control" value="{{ $centro->id_centro }}">
                    <input type="hidden" id="ultimoRegistro" class="form-control" value="hoy">
                    {{ csrf_field() }}
                    <div class="ibox-content">
                        <div class="row">
                            <div class="form-group col-sm-3 label-hleft">
                                <label>Contratante:</label>
                            </div>
                            <div class="col-sm-4"> 
                                <input type="text" id="contratante" class="form-control" value="{{ $centro->contratante }}">

                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3 label-hleft">
                                <label>Teléfono del contratante:</label>
                            </div>
                            <div class="col-sm-4"> 
                                <input type="text" id="tel_contratante" class="form-control" value="{{ $centro->tel_contratante }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="label-hleft form-group col-sm-3 ">
                                <label>Tipo de Proyecto:</label>
                            </div>
                            <div class="col-sm-4"> 
                                <select name="select" class="form-control select-table" id="tiposelect">
                                    <option value="{{ $centro->tipoProyecto->id }}">{{ $centro->tipoProyecto->nombre }}</option>
                                    @foreach($tipos as $tipo)
                                    @if($tipo->nombre != $centro->tipoProyecto->nombre)
                                    <option value="{{ $tipo->id}}">{{ $tipo->nombre }}</option>
                                    @endif
                                    @endforeach

                                </select>


                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3 label-hleft">
                                <label>Nombre del proyecto:</label>
                            </div>
                            <div class="col-sm-4"> 
                                <input type="text" id="nombre_proyecto" name="nombre_proyecto" class="form-control" value="{{ $centro->nombre_proyecto }}">
                            </div>
                        </div>

                    </div>   
                    <div class="ibox-sub-content">
                        <div class="row">       
                            <div class="form-group col-sm-2 col-sm-offset-7">
                                <button type="button" id="button-save" class="btn-primary btn-lg btn btn-block" onclick="save({{ $centro->id_centro }})">Guardar</button>
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
<script src="js/updateProyecto.js"></script>
@stop
