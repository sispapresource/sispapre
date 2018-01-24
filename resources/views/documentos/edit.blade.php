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
                    <h1>Editar {{$documentos->nombre}}</h1>
                    <!-- form guardar catalogo -->
                    <form method="POST" action="{{route('catalogos.documentos.update',['documentos'=>$documentos->id])}}">

                        {{ csrf_field() }}
                        {{ method_field('PATCH')}}

                        <div class="form-group">
                            <label for="nombre">Nombre </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="{{$documentos->nombre}}">
                        </div>
                        &nbsp;
                        &nbsp;
                        <div class="form-group">
                            <div class="form-group">
                                <label for="sel1">Tipos de proyecto a los que aplica:</label>
                                <div>

                                    @foreach($tiposproyecto as $tipo)
                                    @if($tipo->id!=0)
                                    <div class="checkbox">
                                        <label>
                                            @if($documentos->tipoproyecto->contains($tipo)==1)
                                            <input name="tipos[]" type="checkbox" value="{{$tipo->id}}" checked>

                                            @else
                                            <input name="tipos[]" type="checkbox" value="{{$tipo->id}}">

                                            @endif

                                            {{$tipo->nombre}}
                                        </label>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>

                            </div>
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

