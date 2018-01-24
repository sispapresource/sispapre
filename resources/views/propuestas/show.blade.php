@extends('layouts.app')

@section('content')


<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Versiones de la propuesta</h5>
                    </div>
                    <!-- Main content -->
                    <div class="form-group col-sm-2 col-sm-offset-10">

                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <a href="{!! route('propuestas.index') !!}" class="btn btn-default">Regresar</a>
                        </div>
                        <div class="col-md-2 col-md-offset-8">
                            <a id="detailsLinkModal" href="{!! route('createversion',[$propuesta->id]) !!}" class="btn btn-primary">
                                Crear version
                            </a>

                        </div>
                        <div class="col-md-3 col-md-offset-1">
                            <label>Proyecto</label>
                            {{$propuesta->cuenta->nombre_centro}}
                        </div>
                        <div class="col-md-3">
                            <label>Linea de venta:</label>
                            {{$propuesta->lineaDeVenta->nombre}}

                        </div>
                        <div class="col-md-3">
                            <label>Creado por:</label>
                            {{$propuesta->usuario->name}}

                        </div>
                        <div class="col-md-12"><br><br></div>
                        <div class="col-md-10 col-md-offset-1">
                            <table class="table table-hover" >
                                <thead>
                                    <tr>
                                        <th>Version</th>
                                        <th>Fecha de creación</th>
                                        <th>Valida hasta</th>
                                        <th>Monto total</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php $pos=1 ?>
                                    @foreach($versiones as $version)
                                    <tr>
                                        <td><?php print $pos++ ?></td>
                                        <td>{{Carbon\Carbon::parse($version->created_at)->toFormattedDateString()}}</td>
                                        <td>{{Carbon\Carbon::parse($version->valido_hasta)->toFormattedDateString()}}</td>
                                        <td>${{number_format($version->montoTotal(),2)}}</td>
                                        <td>
                                            @php
                                            switch($version->estado->id){
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
                                        <td>@include('versionpropuesta.modal')</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$versiones->links()}}

                        </div>
                    </div>
                    <!-- /Main content -->


                </div>
            </div>


        </div>
    </div>
</div>


@endsection('content')

