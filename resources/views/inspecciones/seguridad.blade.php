@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content">
    <div class="container container-lg">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Seguridad</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row" id="app">
                            <form method="POST" action="{{ url('/home_seguridad') }}">
                                {{ csrf_field() }}
                                <div class="form-group col-sm-3 col-sm-offset-3">
                                    <label for="inputCuenta" class="btn-block">Nombre proyecto</label>
                                    <input name="nombre" type="text" placeholder="Ingrese el nombre del proyecto" class="form-control">
                                </div>
                                <div class="form-group col-sm-3">
                                    <button type="submit" class="btn btn-w-m btn-block btn-gray">Filtrar</button>
                                    <form method="POST" action="{{ url('/home_seguridad') }}">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-w-m btn-block btn-gray">Limpiar</button>
                                    </form>
                                </div>
                            </form>
                        </div>
                        <div>

                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nombre de proyecto</th>
                                        <th>Encargado</th>
                                        <th>Puntaje de ultima Evaluación</th>
                                        <th>Fecha de última Evaluación</th>
                                        <th>Hallazgos pendientes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($centros as $centro)

                                    <tr>
                                        <td>{{$centro->nombre_centro}}</td>
                                        @if($centro->ultimaInspeccion()!= null)
                                        <td>{{$centro->ultimaInspeccion()->encargado}}</td>
                                        <td>{{round($centro->ultimaInspeccion()->puntaje)}}%</td>
                                        <td>{{Carbon\Carbon::parse($centro->ultimaInspeccion()->fecha)->toFormattedDateString()}}
                                            @else
                                        <td>--</td>
                                        <td>0</td>
                                        <td>--</td>
                                        @endif
                                        <td>{{$centro->hallazgos()->count()}}</td>
                                        <td>@include('inspecciones.modal',array('id'=>$centro->id_centro))</td>
                                    </tr>

                                    @endforeach
                                </tbody>
                            </table>
                            {{$centros->links()}}
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
