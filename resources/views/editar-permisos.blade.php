@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Editar Permisos </h5>
                    </div>
                    {{ csrf_field() }}
                    <div class="ibox-content">
                        <div class="row">
                            <div class="label-horizontal form-group col-sm-3 ">
                                    <label>Rol:</label>
                            </div>
                            <div class="col-sm-4" id="div-selectrol"> 
                               <select name="select" class="form-control select-table" id="selectrol">
                                    <option value="" disabled selected hidden>Seleccione el Rol</option>
                                    @foreach ($roles as $rol)
                                        <option value="{{ $rol['id'] }}">{{ $rol['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>    
                    </div>   
                    <div class="ibox-sub-content">
                        <div class="row" id='div-guardar-edit' style="display:none">  
                            <div id="details_roles" class="form-group col-sm-6">
                            </div>     
                            <div class="form-group col-sm-2 col-sm-offset-1">
                                <button type="button" id="button-save" class="btn-primary btn-lg btn btn-block" onclick="update_permisos()">Guardar</button>
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
    <script src="js/rol.js"></script>
@stop