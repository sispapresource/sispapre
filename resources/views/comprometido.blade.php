@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content">
    <div class="container container-lg">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins" id="divGastado">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Detalle de comprometido</h5>
                        <li class="dropdown">
                            <button class="dropdown-toggle btn-primary btn" data-toggle="dropdown" aria-expanded="false">Acción <span class="caret"></span></button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li><a href="{!! route('export_detalle_comprometido', ['centro'=>$idCentro, 'idCuenta'=>'null', 'categoriaFilter'=>'null', 'dateFilterDesde'=>'null', 'dateFilterHasta'=>'null', 'montoFilterDesde'=> 'null', 'montoFilterHasta'=>'null','proveedorFilter'=>'null']) !!}">Exportar detalle de comprometido completo</a></li>
                                <li><a href="{!! route('export_detalle_comprometido', ['centro'=>$idCentro, 'idCuenta'=>$selCuentas ? $selCuentas[0] : 'null', 'categoriaFilter'=>$categorias ? $categorias[0] : 'null', 'dateFilterDesde'=>$fecha_desde ? date_create($fecha_desde)->format('d-m-Y') : 'null', 'dateFilterHasta'=>$fecha_hasta ? date_create($fecha_hasta)->format('d-m-Y') : 'null', 'montoFilterDesde'=>$monto_desde ? $monto_desde : 'null', 'montoFilterHasta'=>$monto_hasta ? $monto_hasta : 'null','proveedorFilter'=>'null']) !!}">Exportar detalle de comprometido filtrado</a></li>
                            </ul>
                        </li>
                    </div>
                    <div class="col-sm-4" id="div-selectcentro"> 
                       
                    </div>
                    <div class="ibox-content">
                        <form class="m-t" role="form" method="POST" action="{{ route('comprometido.filtrar') }}">
                            {{ csrf_field() }}
                            <div class="row" id="app">
                                <div class="form-group col-sm-4">
                                    <label for="selectcentro" class="btn-block">Proyecto</label>
                                    <select name="idCentro" class="form-control" id="idCentro">
                                        @if($idCentro=="") <option value="" disabled selected>Seleccione un centro </option> @endif
                                        @foreach ($proyectos as $proyecto)
                                            <option value="{{ $proyecto->id_centro }}" @if($proyecto->id_centro == $idCentro) selected="selected" @endif>{{ $proyecto->nombre_centro }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="selectcuenta" class="btn-block">Cuenta</label>
                                    <select data-placeholder="Seleccione la(s) cuenta(s)" class="chosen-select" multiple tabindex="4" name="selCuentas[]" id="selCuentas">
                                        @foreach ($cuentas as $cuenta)
                                            <option value="{{ $cuenta->id_cuenta }}" @if($selCuentas && in_array($cuenta->id_cuenta,$selCuentas)) selected="selected"@endif >{{ $cuenta->id_cuenta }} {{ $cuenta->nombre_cuenta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="inputCuenta" class="btn-block">Categoría de costo</label>
                                    <select class="jd-select" multiple="multiple" id="categorias" name="categorias[]">
                                        <option value="MAT" @if($categorias && in_array("MAT", $categorias)) selected="selected" @endif >MAT</option>
                                        <option value="ACE" @if($categorias && in_array("ACE", $categorias)) selected="selected" @endif >ACE</option>
                                        <option value="AST" @if($categorias && in_array("AST", $categorias)) selected="selected" @endif >AST</option>
                                        <option value="CON" @if($categorias && in_array("CON", $categorias)) selected="selected" @endif >CON</option>
                                        <option value="CEM" @if($categorias && in_array("CEM", $categorias)) selected="selected" @endif >CEM</option>
                                        <option value="ARE" @if($categorias && in_array("ARE", $categorias)) selected="selected" @endif >ARE</option>
                                        <option value="BLQ" @if($categorias && in_array("BLQ", $categorias)) selected="selected" @endif >BLQ</option>
                                        <option value="PIE" @if($categorias && in_array("PIE", $categorias)) selected="selected" @endif >PIE</option>
                                        <option value="MDO" @if($categorias && in_array("MDO", $categorias)) selected="selected" @endif >MDO</option>
                                        <option value="PRE" @if($categorias && in_array("PRE", $categorias)) selected="selected" @endif >PRE</option>
                                        <option value="EQP" @if($categorias && in_array("EQP", $categorias)) selected="selected" @endif >EQP</option>
                                        <option value="OTR" @if($categorias && in_array("OTR", $categorias)) selected="selected" @endif >OTR</option>
                                        <option value="SUB" @if($categorias && in_array("SUB", $categorias)) selected="selected" @endif >SUB</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Fecha (Desde)</label>
                                    <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="fechaDesde" name="fechaDesde" v-model="input2" class="form-control" placeholder="Ingrese la Fecha" type="text" value="{{$fecha_desde}}">
                                    </div>
                                </div>
                                <div class="form-group col-sm-4" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Fecha (Hasta)</label>
                                    <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="fechaHasta" name="fechaHasta" v-model="input2" class="form-control" placeholder="Ingrese la Fecha" type="text" value="{{$fecha_hasta}}">
                                    </div>
                                </div>
                                {{--  <div class="form-group col-sm-4">
                                    <label for="inputCuenta" class="btn-block">Proveedor</label>
                                    <input type="text" placeholder="Ingrese el Proveedor" id="proveedor" name="proveedor" class="form-control" v-model="input1" value="{{$nombre_proveedor}}">
                                </div>  --}}
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
                                    <a href="{{ route('comprometido.ver',['idCentro'=>$idCentro]) }}" class="btn btn-w-m btn-block btn-gray">Limpiar</a>   
                                </div>
                            </div>
                        </form>
                        <div id="divFilas">
                            @if (count($comprometidos) > 0)
                            <div class="jqGrid_wrapper">
                                <table id="table_gastado" class ="table table-hover" style="visibility: visible">
                                    <thead>
                                    <tr>
                                        <th>Codigo</th>
                                        <th>Fecha</th>
                                        <th>No. de documento</th>
                                        <th>Descripción</th>
                                        <th>Monto</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($comprometidos as $comprometido)
                                        <tr>
                                            <td>{{ $comprometido->id_cuenta }}</td>
                                            <td>{{ \Carbon\Carbon::parse($comprometido->fecha_transaccion)->toFormattedDateString() }}</td>
                                            <td>{{ $comprometido->num_oc }}</td>
                                            <td>{{ $comprometido->desc_transaccion }}</td>
                                            <td>${{ number_format($comprometido->monto_compra,2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div id="pager_list_4"></div>
                            </div>
                            @else
                                <div class="alert alert-info" role="alert">No se encontraron Registros para este Presupuesto</div>
                            @endif    
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