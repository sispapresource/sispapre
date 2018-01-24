@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Actualizar Avances - {{ $nombre_centro }}</h5>
                    </div>
                    <div class="ibox-content">
                        <form class="m-t" role="form" method="POST" action="{{ url('/update') }}">
                            {{ csrf_field() }}

                            <input type="hidden" id="idCentro" class="form-control" value="{{ $id_centro }}">
                            <div class="row" id="app">
                                <div class="form-group col-sm-4">
                                    <label for="inputCodigo" class="btn-block">Código</label>
                                    <input type="text" placeholder="Ingrese el Código" id="inputCodigo" class="form-control" v-model="input1">
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="inputCuenta" class="btn-block">Cuenta</label>
                                    <input type="text" placeholder="Ingrese la Cuenta" id="inputCuenta" class="form-control" v-model="input2">
                                </div>
                                <div class="form-group col-sm-4">
                                    <button type="button" class="btn btn-w-m btn-block btn-gray" onclick="reload({{ $id_centro }})">Filtrar</button>
                                    <button type="button" class="btn btn-w-m btn-block btn-gray" v-on:click="clear">Limpiar</button>
                                </div>
                            </div>
                        </form>
                        <div class="jqGrid_wrapper">
                            <table id="table_list_3"></table>
                            <div id="pager_list_3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
    <script src="{!! asset('js/update.js') !!}"></script>
@stop

