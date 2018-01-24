@extends('layouts.app') @section('content')

<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Cargar documento - {{$centrocontable->nombre_centro}}</h5>
                    </div>
                    <div class="ibox-content">
                        <h3>Nombre del documento:</h3>
                        <h2>{{$documento->nombre}}</h2>
                        <br><br>
                        <!-- form cargar archivo -->
                        <form class="form" method="POST" action="./cargar" enctype="multipart/form-data">
                            <div class="row">
                                {{ csrf_field() }}
                                <div class="form-group col-sm-4" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Fecha de expiración o revisión(opcional)</label>
                                    <div class="input-group date" id="inputActualizacion">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="fecha" name="fecha" class="form-control" placeholder="Ingrese la Fecha" type="text">
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="inputActualizacion" class="btn-block">Estado</label>
                                    <select id="estado" name="estado" class="form-control">
                                        <option id="estado" name="estado" value="1">Completado</option>
                                        <option id="estado" name="estado" value="0">Pendiente</option>


                                    </select>
                                </div>
                                <div class="form-group col-md-12">

                                    <div class="upload-div" style="padding-left:80px;">
                                        <input name="document" type="file" class="btn btn-gray col-sm-6 col-md-offset-3 b-r" />
                                    </div>

                                </div>
                            </div>

                            <button type="submit" class="btn btn-default">Guardar</button>

                        </form>
                        @include('layouts.errors')
                        <!-- ./form cargar archivo -->
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


@endsection('content')
