@extends('layouts.app')
 
@section('content')
<div class="wrapper wrapper-content">
    <div class="container container-lg">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><i class="fa fa-calculator"></i> Presupuesto de Proyecto - {{ $nombre_centro }} </h5>
                        <li class="dropdown">
                            <button class="dropdown-toggle btn-primary btn" data-toggle="dropdown" aria-expanded="false">Acción<span class="caret"></span></button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li><a class="report" href="{!! route('export_detail', ['tipo'=>'excel', 'centro'=>$id_centro]) !!}">Exportar</a></li>
                                <li><a class="report" href="{!! route('export_detail_corto', ['centro'=>$id_centro]) !!}">Exportar (Corto)</a></li>
                                <li><a class="report" href="{!! route('export_detalle_gastado', ['centro'=>$id_centro, 'idCuenta'=>'null', 'categoriaFilter'=>'null', 'dateFilterDesde'=>'null', 'dateFilterHasta'=>'null', 'montoFilterDesde'=> 'null', 'montoFilterHasta'=>'null','proveedorFilter'=>'null']) !!}">Exportar detalle de costos</a></li>
                                <li style="display:block;"><a href="javascript:exportView({{ $id_centro }});">Exportar vista actual</a></li>
                                <li><a class="report" href="{!! route('export_detail_division', ['centro'=>$id_centro]) !!}">Resumen por division (Gerencial)</a></li>
                                <li><a href="{!! route('export_detail', ['tipo'=>'pdf', 'centro'=>$id_centro]) !!}">Imprimir PDF</a></li>
                                @permission('editar.presupuesto')
                                <li><a href="{{ route('presupuestoavance.create',['id_centro'=>$id_centro])}}">Editar presupuesto</a></li>
                                @endpermission
                                <li><a class="report" href="{!! route('informes_home', ['centro'=>$idc]) !!}">Informe de Codificador de Proyecto</a></li>                                
                            </ul>
                        </li>
                    </div>
                    <div class="ibox-content">
                        <form class="m-t" role="form" method="POST" action="{{ url('/home') }}">
                            {{ csrf_field() }}
                            <input type="hidden" id="idCentro" class="form-control" value="{{ $id_centro }}">
                            <div class="row" id="app">
                                <div class="form-group col-sm-4">
                                    <label for="inputCodigo" class="btn-block">Código</label>
                                    <input type="text" placeholder="Ingrese el Código" id="inputCodigo"
                                           class="form-control" v-model="input1">
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="inputCuenta" class="btn-block">Cuenta</label>
                                    <input type="text" placeholder="Ingrese la Cuenta" id="inputCuenta"
                                           class="form-control" v-model="input2">
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="inputCuenta" class="btn-block">Categoría de costo</label>
                                    <select class="jd-select" multiple="multiple" id="inputCategoria">
                                        <option value="MAT">MAT</option>
                                        <option value="ACE">ACE</option>
                                        <option value="AST">AST</option>
                                        <option value="CON">CON</option>
                                        <option value="CEM">CEM</option>
                                        <option value="ARE">ARE</option>
                                        <option value="BLQ">BLQ</option>
                                        <option value="PIE">PIE</option>
                                        <option value="MDO">MDO</option>
                                        <option value="PRE">PRE</option>
                                        <option value="EQP">EQP</option>
                                        <option value="OTR">OTR</option>
                                        <option value="SUB">SUB</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Fecha (Desde)</label>
                                    <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="dateFilterDesde" v-model="input2" class="form-control" placeholder="Ingrese la Fecha" type="text">
                                    </div>
                                </div>
                                <div class="form-group col-sm-4" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Fecha (Hasta)</label>
                                    <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="dateFilterHasta" v-model="input2" class="form-control" placeholder="Ingrese la Fecha" type="text">
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <button type="button" class="btn btn-w-m btn-block btn-gray" onclick="reload( {{$id_centro}} ,'find')">Filtrar</button>
                                    <button type="button" class="btn btn-w-m btn-block btn-gray" onclick="reload( {{$id_centro}} ,'clear')">Limpiar</button>
                                </div>
                            </div>
                        </form>
                        <div class="jqGrid_wrapper">
                            <table id="table_list_2"></table>
                            <div id="pager_list_2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script src="{!! asset('js/detail.js') !!}"></script>
@stop
