@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Documentos de {{$centro->nombre_centro}}</h5>
                        <li class="dropdown">
                            <button class="dropdown-toggle btn-primary btn" data-toggle="dropdown" aria-expanded="false">Acción <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{route('catalogos.documentos.create',['idCentro'=>$centro->id_centro])}}">Crear documento</a></li>
                            </ul>
                        </li>
                    </div>
                    <div class="ibox-content">

                        <div class="row" id="app">
                            <form method="POST" action="{{route('documentos.index',['idCentro'=>$centro->id_centro])}}">
                                {{ csrf_field() }}
                                <div class="form-group col-sm-3">
                                    <label for="inputCuenta" class="btn-block">Nombre</label>
                                    <input type="text" name="nombre" placeholder="Ingrese el nombre del documento" id="nombre" class="form-control">
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group col-sm-6" id="data_1">
                                        <label for="inputActualizacion" class="btn-block">Inicio</label>
                                        <div class="input-group date" id="inputActualizacion" >
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input id="inicio" name="inicio" class="form-control" placeholder="Ingrese la Fecha" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6" id="data_1">
                                        <label for="inputActualizacion" class="btn-block">Fin</label>
                                        <div class="input-group date" id="inputActualizacion" >
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input id="fin" name="fin" class="form-control" placeholder="Ingrese la Fecha" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-5">
                                    <button type="submit" class="btn btn-w-m btn-block btn-gray">Filtrar</button>
                                    <form method="POST" action="./documentos?idCentro={{$centro->id_centro}}">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-w-m btn-block btn-gray">Limpiar</button>
                                    </form>
                                </div>
                            </form>


                        </div>
                        <div class="ibox-content">
                            <table class="table table-hover" >
                                <thead >
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Requerido/Opcional </th>
                                        <th>Ultima Version Cargada</th>
                                        <th>Fecha de carga</th>
                                        <th>Cargado por</th>
                                        <th>Estado</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documentos as $documento)
                                    <tr>
                                        <td>
                                            {{$documento->nombre}}
                                        </td>
                                        <td>
                                            @if($documento->requerido==1)
                                            Requerido
                                            @else
                                            Opcional
                                            @endif
                                        </td>
                                        @if($documento->pivot->url!=null)
                                        <td>
                                            <a href="{{$documento->id}}/{{$centro->id_centro}}/descargar">
                                                {{$documento->pivot->url}}
                                            </a>
                                        </td>
                                        <td>
                                            {{Carbon\Carbon::parse($documento->pivot->fecha_de_carga)->toFormattedDateString()}}
                                        </td>
                                        <td>
                                            {{$documento->usuario->first()->name}}
                                        </td>
                                        @if($documento->pivot->estado==1)
                                        <td align="center" style="color:black; background-color:#9ACD32;border-radius:10px;padding-top:15px;">
                                            Completado
                                        </td>
                                        @else
                                        <td align="center" style="color:black; background-color:#FF0000;border-radius:10px;padding-top:15px;">
                                            Pendiente
                                        </td>
                                        @endif
                                        @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        @endif
                                        <td>
                                            @include('documentos.modal')
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>
</div>


@endsection('content')
