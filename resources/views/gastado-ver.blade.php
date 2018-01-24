@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content">
    <div class="container container-lg">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins" id="divGastado">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Detalle de gastado</h5>
                        <li class="dropdown">
                            <button class="dropdown-toggle btn-primary btn" data-toggle="dropdown" aria-expanded="false">Acción <span class="caret"></span></button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li><a href="{!! route('export_detalle_gastado', ['centro'=>$id_centro, 'idCuenta'=>'null', 'categoriaFilter'=>'null', 'dateFilterDesde'=>'null', 'dateFilterHasta'=>'null', 'montoFilterDesde'=> 'null', 'montoFilterHasta'=>'null','proveedorFilter'=>'null']) !!}">Exportar detalle de costos completo</a></li>
                                <li><a href="{!! route('export_detalle_gastado', ['centro'=>$id_centro, 'idCuenta'=>$id_cuenta ? implode(',',$id_cuenta) : 'null', 'categoriaFilter'=>$categorias ? implode(',',$categorias) : 'null', 'dateFilterDesde'=>$fecha_desde ? date_create($fecha_desde)->format('d-m-Y') : 'null', 'dateFilterHasta'=>$fecha_hasta ? date_create($fecha_hasta)->format('d-m-Y') : 'null', 'montoFilterDesde'=>$monto_desde ? $monto_desde : 'null', 'montoFilterHasta'=>$monto_hasta ? $monto_hasta : 'null','proveedorFilter'=>$nombre_proveedor ? $nombre_proveedor : 'null']) !!}">Exportar detalle de costos filtrado</a></li>
                            </ul>
                        </li>
                    </div>
                    <div class="col-sm-4" id="div-selectcentro"> 

                    </div>
                    <div class="ibox-content">
                        <form class="m-t" role="form" method="POST" action="{{ route('gastado.filtrar') }}">
                            {{ csrf_field() }}
                            <div class="row" id="app">
                                <div class="form-group col-sm-4">
                                    <label for="selectcentro" class="btn-block">Proyecto</label>
                                    {{ Form::select('idCentro', $proyectos , $id_centro,['class'=>'form-control']) }}
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="selectcuenta" class="btn-block">Cuenta</label>
                                    <select data-placeholder="Seleccione la(s) cuenta(s)" 
                                    class="chosen-select form-control" multiple tabindex="4" 
                                    name="cuentas[]" id="cuentas">
                                        @foreach ($cuentas as $cuenta)
                                            <option value="{{ $cuenta->id_cuenta }}"
                                            @if($id_cuenta && in_array($cuenta->id_cuenta,$id_cuenta)) selected="selected"@endif>
                                                {{ $cuenta->id_cuenta }} 
                                                {{ $cuenta->nombre_cuenta }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="inputCuenta" class="btn-block">Categoría de costo</label>
                                    
                                    <select class="jd-select form-control" multiple="multiple" name="categorias[]" id="categorias">
                                        <option value="MAT"
                                        @if($categorias && in_array("MAT",$categorias)) selected="selected"@endif>MAT</option>
                                        <option value="ACE"
                                        @if($categorias && in_array("ACE",$categorias)) selected="selected"@endif>ACE</option>
                                        <option value="AST"
                                        @if($categorias && in_array("AST",$categorias)) selected="selected"@endif>AST</option>
                                        <option value="CON"
                                        @if($categorias && in_array("CON",$categorias)) selected="selected"@endif>CON</option>
                                        <option value="CEM">CEM</option>
                                        @if($categorias && in_array("CEM",$categorias)) selected="selected"@endif
                                        <option value="ARE"
                                        @if($categorias && in_array("ARE",$categorias)) selected="selected"@endif>ARE</option>
                                        <option value="BLQ"
                                        @if($categorias && in_array("BLQ",$categorias)) selected="selected"@endif>BLQ</option>
                                        <option value="PIE"
                                        @if($categorias && in_array("PIE",$categorias)) selected="selected"@endif>PIE</option>
                                        <option value="MDO"
                                        @if($categorias && in_array("MDO",$categorias)) selected="selected"@endif>MDO</option>
                                        <option value="PRE"
                                        @if($categorias && in_array("PRE",$categorias)) selected="selected"@endif>PRE</option>
                                        <option value="EQP"
                                        @if($categorias && in_array("EQP",$categorias)) selected="selected"@endif>EQP</option>
                                        <option value="OTR"
                                        @if($categorias && in_array("OTR",$categorias)) selected="selected"@endif>OTR</option>
                                        <option value="SUB"
                                        @if($categorias && in_array("SUB",$categorias)) selected="selected"@endif>SUB</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Fecha (Desde)</label>
                                    <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input id="dateDesde" name="dateDesde" v-model="input2" class="form-control" placeholder="Ingrese la Fecha" type="text" value="{{$fecha_desde}}">
                                    </div>
                                </div>
                                <div class="form-group col-sm-4" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Fecha (Hasta)</label>
                                    <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input id="dateHasta" name="dateHasta" v-model="input2" class="form-control" placeholder="Ingrese la Fecha" type="text" value="{{$fecha_hasta}}">
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="inputCuenta" class="btn-block">Proveedor</label>
                                    <input type="text" placeholder="Ingrese el Proveedor" id="proveedor" name="proveedor" class="form-control" v-model="input1" value="{{$nombre_proveedor}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 form-group"> 
                                    <label for="inputCuenta" class="btn-block">Monto (Desde)</label>
                                    <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                        <input class="form-control amountNew" type="text" id="montoDesde" name="montoDesde" placeholder="Ingrese el Monto" value="{{$monto_desde}}">
                                    </div>  
                                </div>
                                <div class="col-sm-4 form-group"> 
                                    <label for="inputCuenta" class="btn-block">Monto (Hasta)</label>
                                    <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                        <input class="form-control amountNew" type="text" id="montoHasta" name="montoHasta" placeholder="Ingrese el Monto" value="{{$monto_hasta}}">
                                    </div>  
                                </div>
                                <div class="form-group col-sm-4">
                                    <button type="submit" class="btn btn-w-m btn-block btn-gray" >Filtrar</button>
                                    <a href="{{ route('gastado.ver',['idCentro'=>$id_centro]) }}" class="btn btn-w-m btn-block btn-gray">Limpiar</a>   
                                </div>
                            </div>
                        </form>
                        <div id="divFilas">

                            <div class="jqGrid_wrapper">
                                <table id="table_gastado" style="visibility: visible" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Fecha</th>
                                            <th>Proveedor</th>
                                            <th>No. de documento</th>
                                            <th>Descripción</th>
                                            <th>Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paginator->getCollection() as $item)
                                        <tr>
                                            <td>
                                                {{$item['id_cuenta']}}
                                            </td>
                                            <td>
                                                {{\Carbon\Carbon::parse($item['fecha_transaccion'])->toFormattedDateString()}}
                                            </td>
                                            <td>
                                                @if(array_key_exists('nombre_proveedor',$item))
                                                {{$item['nombre_proveedor']}}
                                                @endif
                                            </td>
                                            <td>
                                                @if(array_key_exists('num_planilla',$item))
                                                    {{$item['num_planilla']}}
                                                @else
                                                    {{$item['num_fact']}}
                                                @endif
                                            </td>
                                            <td>
                                                {{$item['desc_transaccion']}}
                                            </td>
                                            <td>
                                                {{number_format($item['monto'],2)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                                {{$paginator}}
                                <div id="pager_list_4"></div>
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

<script src="{!! asset('js/gastado.js') !!}"></script>
@stop