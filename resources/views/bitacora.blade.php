@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Log de Cambios al Presupuesto - {{ $nombre_centro }}</h5>
                    </div>
                    <div class="ibox-content">
                        @if (count($adendas) > 0)
                        <div class="jqGrid_wrapper">
                            <table id="table_list_4" style="visibility: visible">
                                <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cambio por</th>
                                    <th>Descripción</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($adendas as $adenda)
                                    <tr>
                                        <td>{{ $adenda['fecha_transaccion'] }}</td>
                                        <td>{{ $adenda['usuario'] }}</td>
                                        <td>{!! $adenda['id'] !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div id="pager_list_4"></div>
                        </div>
                        @else
                            <div class="alert alert-info" role="alert">No se encontraron Registros de Cambios para este Presupuesto</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
    <script src="{!! asset('js/bitacora.js') !!}"></script>
@stop