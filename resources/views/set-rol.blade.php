@extends('layouts.app')

@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Asignar Roles - Ver Proyectos </h5>
                    </div>
                    {{ csrf_field() }}
                    <div class="ibox-content" id="divuser">
                        <div class="row">
                            <div class="label-hleft form-group col-sm-3">
                                <label>Usuario:</label>
                            </div>
                            <div class="col-sm-4" > 
                               <select name="select" class="form-control select-table" id="selectuser">
                                    <option value="" disabled selected hidden>Seleccione un Usuario</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user['id'] }}_{{ $user['rol'] }}">{{ $user['name'] }} ({{ $user['email'] }})</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-5" id="rol_desc" style="text-align:center;"></div>
                        </div>
                    </div>  
                    <div id="div-rol" style="display:none">
                        <div class="ibox-sub-title">
                            <h5>Si desea cambiar el rol del usuario seleccione uno nuevo:</h5>
                        </div>
                        <div class="row ibox-sub-content-normal">
                            <div class="label-hleft form-group col-sm-3 ">
                                    <label>Rol:</label>
                            </div>
                            <div class="col-sm-4"> 
                               <select name="select" class="form-control select-table" id="tiporol">
                                    <option value="" disabled selected hidden>Seleccione un Nuevo Rol</option>
                                    @foreach ($roles as $rol)
                                        <option value="{{ $rol['id'] }}">{{ $rol['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-5" id="div-save-usuario-rol" style="display:none">
                                <button type="button" id="button-save" class="btn-primary btn btn-block" onclick="save()">Guardar</button>
                            </div>
                        </div>   
                    </div> 
                    <div id="div-proyectos" style="display:none">
                        <div class="ibox-sub-title">
                            <h5>Proyectos visibles para el usuario seleccionado</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div id="details_centros_user" class="form-group col-sm-12"></div>
                            </div>
                        </div>
                    </div>
                </div>                          
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="js/rol.js"></script>
@stop