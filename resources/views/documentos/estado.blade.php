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
                    <h1>Cambiar estado </h1>
                    <h2>Documento: {{$documento->nombre}}</h2>
                    <h2>Centro {{$centrocontable->nombre_centro}}</h2>
                    <!-- form guardar catalogo -->
                    <form class="" method="POST" action="./docestado">

                        {{ csrf_field() }}
                        <div class row>
                            <br><br><br>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <select id="estado" name="estado" class="form-control">
                                            @if($centrocontable->documentos()->where('documento_id',$documento->id)->first()->pivot->estado==1)
                                            <option id="estado" name="estado" value="0">Pendiente</option>
                                            <option id="estado" name="estado" value="1">Completado</option>

                                            @else
                                            <option id="estado" name="estado" value="1">Completado</option>
                                            <option id="estado" name="estado" value="0">Pendiente</option>

                                            @endif


                                        </select>
                                    </div>
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

