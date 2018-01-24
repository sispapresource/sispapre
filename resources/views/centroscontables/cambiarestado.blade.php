@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Cambiar estado</h5>
                    </div>

                    <h1>{{$centro->nombre_centro}}</h1>
                    <form method="POST" action="./guardarestado?idCentro={{$centro->id_centro}}">

                        <div class="row">
                            <div class="col-md-6">

                                {{csrf_field()}}

                                <div class="form-group">
                                    <label for="estado">Estado: </label>
                                    <select name="estados" name="estados" class="form-control">
                                        <option value="1">Activo</option>
                                        <option value="2">En Revisión</option>
                                        <option value="3">Suspendido</option>
                                        <option value="4">Inactivo</option>
                                    </select>

                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Cambiar Estado</button>
                                </div>

                            </div>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>

@endsection







