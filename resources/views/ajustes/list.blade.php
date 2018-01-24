@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Ajustes - Adenda {{ $numero_adenda }} - {{ $nombre_centro }}</h5>
                    </div>
                    <input type="hidden" id="idAdenda" class="form-control" value="{{ $id_adenda }}">
                    <div class="ibox-content">
                        <div class="row" id="app">
                            <form class="m-t" role="form" method="POST" action="{{ url('/home') }}">
                                {{ csrf_field() }}
                                <div class="form-group col-sm-4">
                                    <label for="inputCuenta" class="btn-block">Nombre centro</label>
                                    <input type="text" placeholder="Ingrese el nombre del centro" id="textFilter" class="form-control" v-model="input1">
                                </div>
                                <div class="form-group col-sm-4" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Rango de fecha de adenda</label>
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
                            <div id="noregister" class="alert alert-info" role="alert">No se encontraron Ajustes</div>
                            <table id="table_list_6"></table>
                            <div id="pager_list_6"></div>
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
                                            @permission('crear.adenda')
                                                 <a id="editAjusteLinkModal" href="" class="btn btn-lg btn-primary btn-block btn-modal">Editar Ajuste</a>
                                            @endpermission  
                                            <a id="viewAdendaLinkModal" href="{{ url('#') }}" class="btn btn-lg btn-primary btn-block btn-modal">Ver Items</a>
                                            <a id="createItemLinkModal" href="{{ url('/item_crear') }}" class="btn btn-lg btn-primary btn-block btn-modal">Crear Item</a>
                                            <a id="exportAjusteExcel" href="" class="btn btn-lg btn-primary btn-block btn-modal">Exportar ajuste en Excel</a>
                                            <a id="exportAjustePdf" href="" class="btn btn-lg btn-primary btn-block btn-modal">Exportar ajuste en PDF</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="modal-upload" class="modal fade" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <form role="form" method="POST" action="{{ url('/upload') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="idAdenda" id="idAdenda">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h3><span>Subir Documentos</span></h3>
                                    </div>
                                    <div class="modal-body">
                                        <div class="upload-div">
                                            <input name="document" type="file" class="btn btn-gray"/>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="col-sm-6 b-r">
                                            <button type="button" class="btn btn-gray btn-block" data-dismiss="modal">Close</button>
                                        </div>
                                        <div class="col-sm-6 b-r">
                                            <button type="submit" class="btn btn-primary btn-block">Guardar</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                        <table id="tDocuments" class="table table-files table-bordered">
                            @if (count($documents) > 0) 
                            <tr>
                                <th>Nombre del archivo</th>
                                <th>Fecha y hora de subida</th>
                                <th>Usuario creador</th>
                            </tr>
                            @foreach ($documents as $document)
                                <tr>
                                    <td><a href="{{ $document->url }}" target="_blank">{{ $document->name_file }}</a></td>
                                    <td>{{ $document->created_at }}</td>
                                    <td>{{ $document->name_user }}</td>
                                </tr>
                            @endforeach
                            @endif

                        </table>
                    </div>
            </div>
        </div>
    </div>
</div>
                
@endsection

@section('page-script')
    <script src="js/ajustes.js"></script>
@stop
