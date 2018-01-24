@extends('layouts.app')

@section('content')


<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Version</h5>
                    </div>
                    <!-- Main content -->
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            {!!  Form::open(array('route' => array('saveestado', $version->id))) !!}

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>Cliente</label>
                                        {{$version->propuesta->cuenta->nombre_centro}}
                                    </div>
                                    <div class="col-md-4">
                                        <label>Linea de venta:</label>
                                        {{$version->propuesta->lineaDeVenta->nombre}}

                                    </div>
                                    <div class="col-md-4">
                                        <label>Creado por:</label>
                                        {{$version->propuesta->usuario->name}}
                                    </div>
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <label>Fecha de creacion</label><br>
                                        {{$version->fecha_creacion}}
                                    </div>
                                    <div class="col-md-12">
                                        <label>Monto total</label><br>
                                        {{$version->monto_total}}
                                    </div>
                                    <div class="col-md-12">
                                        <label>Valida hasta</label><br>
                                        {{$version->valido_hasta}}
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <!-- No propuesta Field -->
                                    <div class="form-group  ">
                                        {!! Form::label('estado', 'Seleccione un estado:') !!}<br>
                                        {!!Form::select('estado',$estados)!!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-3 col-sm-offset-5">
                                {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                                <a  href="{!! route('propuestas.show',[$propuesta->id]) !!}"  class="btn btn-default">Cancelar</a>
                            </div>

                            {!! Form::close() !!}

                        </div>
                        <div class="col-md-10 col-md-offset-1">
                            @include('layouts.errors')
                        </div>
                    </div>
                    <!-- /Main content -->


                </div>
            </div>


        </div>
    </div>
</div>


@endsection('content')

