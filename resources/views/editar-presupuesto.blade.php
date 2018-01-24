@extends('layouts.app')
 
@section('content')
<div class="wrapper wrapper-content">
    <div class="container container-lg">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><i class="fa fa-calculator"></i> Presupuesto de Proyecto - {{ $nombre_centro }} </h5>
                    </div>
                    <div class="ibox-content">
                        <form class="m-t" role="form" method="POST" action="{{ url('/home') }}">
                            {{ csrf_field() }}
                            <input type="hidden" id="idCentro" class="form-control" value="{{ $id_centro }}">
                        </form>
                        <h2>Agregar cuentas: </h2>
                        <hr>
                        <form action="{{route('agregar.cuenta.centro',['centro'=>$id_centro])}}" method="POST">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-md-1">
                                    <label for="agregar_cuenta">Cuenta</label>
                                </div>
                                <div class="col-md-4 ui-widget">
                                    <input type="text" id="agregar_cuenta" name="agregar_cuenta" class="form-control">
                                </div>
                            
                                <div class="col-md-4">
                                    {{Form::submit('Agregar cuentas',['class'=>'btn btn-primary'])}}
                                </div>
                            </div>
                            <div id="test"></div>
                        </form>
                        @if(Session::has('cuentas'))
                            <hr>

                                <div class="alert alert-success" role="alert">
                                    <strong>Éxito!</strong> Se agrego la cuenta {{Session::get('cuentas')}} 
                                </div>

                        @endif
                        <hr>
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

<script src="{!! asset('js/plugins/JQueryUI/jquery-ui.js') !!}"></script>
<script src="{!! asset('js/details-edit.js') !!}"></script>
@stop
