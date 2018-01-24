@extends('layouts.app')

@section('content')


<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Catalogos</h5>
                    </div>
                    <h1>Editar {{$proyecto->nombre}}</h1>
                    <!-- form guardar catalogo -->
                    <form method="POST" action="{{route('catalogos.proyecto.update',['proyecto'=>$proyecto->id])}}">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        <div class="form-group">
                            <label for="nombre">Nombre </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="{{$proyecto->nombre}}">
                        </div>
                        &nbsp;
                        &nbsp;
                        <div class="form-group">
                            <label for="descripcion">Descripcion </label>
                            <textarea id="descripcion" name="descripcion" class="form-control" value="$tipoproyecto->descripcion">{{$proyecto->descripcion}}</textarea>
                        </div>
                        &nbsp;&nbsp;

                        <button type="submit" class="btn btn-default">Guardar</button>

                    </form>
                    @include('layouts.errors')
                    <!-- ./form guardar catalogo -->

                </div>
            </div>


        </div>
    </div>
</div>




@endsection('content')

