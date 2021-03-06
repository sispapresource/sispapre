@extends('layouts.app')

@section('content')


<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Crear version</h5>
                    </div>
                    <!-- Main content -->
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            {!! Form::open(['route' => 'versiones.store','method' => 'post','files'=>true]) !!}
                            @include('versionpropuesta.fields')
                            <input name="idPropuesta" id="idPropuesta" type="text" value="{{$propuesta->id}}" hidden/>

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

