@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Inspecciones - {{ $nombre_centro }}</h5>
                        <li class="dropdown">
                            <a  href="{{ url('/evaluacion_crear?idCentro='.$id_centro) }}" class="dropdown-toggle btn-primary btn" style="color:white;">Crear</a>
                        </li>
                    </div>
                    <input type="hidden" id="idCentro" class="form-control" value="{{ $id_centro }}">
                    <div class="ibox-content">
                        <div class="jqGrid_wrapper">
                            <div id="noregister" class="alert alert-info" role="alert">No se encontraron Inspecciones</div>
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
                                    <h3>Opciones: <span style="color:#337ab7">Inspección </span><span id="nameProject" style="color:#337ab7"></span></h3>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <div id="first3">
                                                <a id="editInspeccionLinkModal" href="" class="btn btn-lg btn-primary btn-block btn-modal">Editar</a>
                                                <a id="descargarInspeccionLinkModal" class="btn btn-lg btn-primary btn-block btn-modal" href="">Descargar documento</a>
                                                <a id="cambiarEstadoInspeccionLinkModal" href="#" class="btn btn-lg btn-primary btn-block btn-modal">Cambiar estado</a>
                                            </div>
                                            <div id="last3" style="display:none;">
                                                <a id="cambiarRevisionInspeccionLinkModal" href="" class="btn btn-lg btn-primary btn-block btn-modal" style="color:black; background-color:#ED7D31; border:0;">En revisión</a>
                                                <a id="cambiarRevisadaInspeccionLinkModal" class="btn btn-lg btn-primary btn-block btn-modal" href="" style="color:black; background-color:#70AD47;border:0;">Revisada</a>
                                                <a id="cambiarAnuladaInspeccionLinkModal" href="#" class="btn btn-lg btn-primary btn-block btn-modal" style="color:black; background-color:#AFABAB;border:0;">Anulada</a>
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
</div>
                
@endsection

@section('page-script')
    <script src="js/inspecciones.js"></script>
@stop

