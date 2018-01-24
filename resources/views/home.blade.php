@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    {{ csrf_field() }}
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Proyectos</h5>
                        <li class="dropdown">
                            <button class="dropdown-toggle btn-primary btn" data-toggle="dropdown" aria-expanded="false">Acción <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{!! route('export_home') !!}">Exportar</a></li>
                            </ul>
                        </li>
                    </div>
                    @permission('ver.facturacion_cobro')
                    <input type="hidden" id="verFacturasCobros" class="form-control" value="1">
                    @endpermission
                    <div class="ibox-content">
                        <div class="row" id="app">
                            <form class="m-t" role="form" method="POST" action="{{ url('/home') }}">
                                <div class="form-group col-sm-4">
                                    <label for="inputCuenta" class="btn-block">Nombre centro</label>
                                    <input type="text" placeholder="Ingrese el nombre del centro" id="textFilter" name="textFilter" class="form-control" v-model="input1">
                                </div>
                                <div class="form-group col-sm-4" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Última actualización antes de</label>
                                    <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="dateFilter" v-model="input2" class="form-control" placeholder="Ingrese la Fecha" type="text">
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <button type="button" class="btn btn-w-m btn-block btn-gray" onclick="reload()">Filtrar</button>
                                    <button type="button" class="btn btn-w-m btn-block btn-gray" onclick="reload('clear')">Limpiar</button>
                                </div>
                            </form>
                        </div>
                        <div class="jqGrid_wrapper">
                            <table id="table_list_1"></table>
                            <div id="pager_list_1"></div>
                        </div>    
                    </div>                              
                    <div id="modal-form" class="modal fade" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h3>Opciones: <span id="nameProject" style="color:#337ab7"></span></h3>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-12 b-r"> 
                                            @permission('ver.presupuesto')
                                            <a id="detailsLinkModal" href="{{ url('/details') }}" class="btn btn-lg btn-primary btn-block btn-modal">Ver Presupuesto</a>
                                            @endpermission
                                            @permission('editar.presupuesto.detalle')
                                            <a id="presupuestoManual" href="{{ url('/details-edit') }}" class="btn btn-lg btn-primary btn-block btn-modal">Actualizar presupuesto</a>
                                            @endpermission
                                            <a id="graficoLinkModal" href="{{ url('/grafico_cuentas') }}" class="btn btn-lg btn-primary btn-block btn-modal">Ver Dashboard Grafico</a>
                                            <!--<a id="graficoMLinkModal" href="{{ url('/grafico_test') }}" class="btn btn-lg btn-primary btn-block btn-modal">Ver Grafico Mixto</a>-->
                                            <a id="gastadoLinkModal" href="{{ url('/gastado') }}" class="btn btn-lg btn-primary btn-block btn-modal">Ver detalle de costos y gastos</a> 
                                            {{--  @permission('actualizar.avances')     --}}
                                            <a id="updateLinkModal" href="{{ url('/update') }}" class="btn btn-lg btn-primary btn-block btn-modal">Actualizar Avances</a>
                                            {{--  @endpermission   --}}
                                            @permission('crear.adenda')
                                            <a id="adendaLinkModal" href="{{ url('/adenda_crear') }}" class="btn btn-lg btn-primary btn-block btn-modal">Crear Adenda</a>
                                            @endpermission 
                                            @permission('ver.bitacora')
                                            <a id="bitacoraLinkModal" href="{{ url('/bitacora') }}" class="btn btn-lg btn-primary btn-block btn-modal">Ver Bitácora</a>
                                            @endpermission 
                                            @permission('exportar.presupuesto')
                                            <a id="" href="#" class="btn btn-lg btn-primary btn-block btn-modal">Exportar Presupuesto</a>
                                            @endpermission 
                                            @permission('actualizar.detalles_proyecto')
                                            <a id="updateDetailsLinkModal" href="{{ url('/update_details') }}" class="btn btn-lg btn-primary btn-block btn-modal">Actualizar Detalles del Proyecto</a>
                                            @endpermission  
                                            @permission('cambiar.estado')
                                            <a id="updateEstadoProyecto" href="{{ url('/cambiarestado') }}" class="btn btn-lg btn-primary btn-block btn-modal">Cambiar estado del proyecto</a>
                                            @endpermission  

                                            @permission('ver.documentacion')
                                            <a id="documentosProyecto" href="{{route('documentos.index')}}" class="btn btn-lg btn-primary btn-block btn-modal">Documentos</a>
                                            @endpermission  
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                </div>
            </div>
        </div>
    </div>
</div>

@include('modal-centros')

@endsection

@section('page-script')
<script src="js/dashboard.js"></script>
@stop







