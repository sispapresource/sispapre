@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Log de Usuarios</h5>
                    </div>
                    <div class="ibox-content">
                        @if (count($login_usuarios) > 0)
                        <div class="jqGrid_wrapper">
                            <table id="table_list_4" style="visibility: visible">
                                <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($login_usuarios as $logs)
                                    <tr>
                                        <td>{{ $logs['fecha'] }}</td>
                                        <td>{{ $logs['usuario'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div id="pager_list_4"></div>
                        </div>
                        @else
                            <div class="alert alert-info" role="alert">No se encontraron Login</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
    <script src="{!! asset('js/log_login.js') !!}"></script>
@stop