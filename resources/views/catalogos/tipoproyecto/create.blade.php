@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Documentos de </h5>
                    </div>
                    <div class="ibox-content">

                        <!-- form guardar catalogo -->
                        <form class="" method="POST" action="{{route('catalogos.proyecto.store')}}">
                            {{ csrf_field() }}
                            @include('catalogos.tipoproyecto.fields');
                            <button type="submit" class="btn btn-default">Guardar</button>
                        </form>
                        @include('layouts.errors')
                        <!-- ./form guardar catalogo -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection('content')

