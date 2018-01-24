@extends('layouts.app')

@section('content')


<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Propuestas</h5>
                    </div>
                    <!-- Main content -->

                    <div class="row">
                        <div class="col-md-2 col-md-offset-10">
                        @permission('crear.propuestas')
                            {!! Form::open(['route' => 'propuestas.create','method' => 'get']) !!}
                            <input type="submit"  class="btn btn-primary" value="Crear">
                            {!! Form::close() !!}
                        @endpermission
                        </div>
                        <div class="col-md-10 col-md-offset-1">
                            <table class="table table-hover" >
                                <thead>
                                    <tr>
                                        <th>Proyecto</th>
                                        <th>Cliente</th>
                                        <th>No. de propuesta</th>
                                        <th>Linea de venta</th>
                                        <th>Versiones</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($propuestas as $propuesta)
                                    <tr>
                                        <th scope="row">{{$propuesta->cuenta->nombre_centro}}</th>
                                        <th>
                                            @foreach($propuesta->clientes as $cliente)
                                                {{$cliente->nombre}}<br>
                                            @endforeach
                                        </th>
                                        <td>{{$propuesta->no_de_propuesta}}</td>
                                        <td>{{$propuesta->lineaDeVenta->nombre}}</td>
                                        <td>{{$propuesta->versiones()}}</td>
                                        <td>
                                            @php
                                            switch($propuesta->estado()){
                                            case 1:
                                            echo '<span class="label label-success" style="font-size: 16px;">En desarrollo</span>';
                                            break;
                                            case 2:
                                            echo '<span class="label label-primary" style="font-size: 16px;">Vigente</span>';
                                            break;
                                            case 3:
                                            echo '<span class="label label-warning" style="font-size: 16px;">Ganada</span>';
                                            break;
                                            case 4:
                                            echo '<span class="label label-danger" style="font-size: 16px;">Perdida</span>';
                                            break;
                                            case 5:
                                            echo '<span class="label label-default" style="font-size: 16px;">Anulada</span>';
                                            break;
                                            }

                                            @endphp
                                        </td>
                                        <td>@include('propuestas.modal')</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$propuestas->links()}}

                        </div>
                    </div>
                    <!-- /Main content -->


                </div>
            </div>


        </div>
    </div>
</div>


@endsection('content')

