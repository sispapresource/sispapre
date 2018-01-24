@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Crear ítem a ajuste - Ajuste {{ $nro_ajuste }} - Adenda {{ $nro_adenda }}</h5>
                    </div>
                    <input type="hidden" id="idCentro" class="form-control" value="{{ $id_centro }}">
                    {{ csrf_field() }}
                    <div class="ibox-content">
                        <div class="row">
                            <div class="form-group col-sm-2">
                                <label for="inputActualizacion" class="btn-block">Número de ítem</label>
                                <input style="text-align:center;" class="form-control" type="text" class="form-control" id="nro_item">
                            </div>       
                        </div>
                    </div>   
                    <div class="ibox-sub-title">
                        <h5>Modificaciones</h5>
                    </div>    
                    <div class="ibox-sub-content">
                        <div id="adendas">
                            <button type="button" class="btn btn-gray btn-add" id="add"><i class="fa fa-plus" title="Add"></i></button>
                            <div class="row divDetail" id="divsd">       
                                <div class="form-group col-sm-3 ui-widget">
                                    <label for="inputCuenta" class="btn-block">Cuenta</label>
                                    <select name="select" class="form-control select-table" id="select-tablesd">
                                        <option value="" disabled selected hidden>Seleccione una cuenta</option>
                                    </select>
                                </div>
                                <div style="display:none" id="divamountOld">
                                    <div class="form-group col-sm-3">
                                        <label for="inputActualizacion" class="btn-block">Monto actual</label>
                                        <div class="input-group" id="inputActualizacion" >
                                            <span class="input-group-addon usd-off"><i class="fa fa-usd"></i></span>
                                            <input class="form-control amountOld" type="text" disabled="disabled" id="select-tablesdamountOld">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 form-group"> 
                                        <label for="inputCuenta" class="btn-block">Costo</label>
                                        <div class="input-group date" id="inputActualizacion" >
                                            <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                            <input class="form-control amountNew" type="text" id="amountNew-sd">
                                        </div>  
                                    </div>
                                    <div class="col-sm-3 button-top">
                                        <button type="button" class="btn btn-gray" onclick="deleteDiv('sd')" id="buttonDeletetablesd" style="display:none;"><i class="fa fa-trash-o" title="Delete"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="display:none" id="divAdenda">    
                            <div class="row button-top">       
                                <div class="col-sm-offset-3 form-group col-sm-3 label-horizontal">
                                    <label>Costo Total:</label>
                                </div>
                                <div class="col-sm-3"> 
                                   <div class="input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                        <input class="form-control" type="text" disabled="disabled" id="amountAdenda">
                                    </div>
                                </div>
                            </div>
                            <div class="row">       
                                <div class="form-group col-sm-6">
                                    <label for="observaciones" class="btn-block">Observaciones</label>
                                    <textarea style="resize: none;" name="observaciones" rows="3" id="observaciones" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="row">       
                                <div class="form-group col-sm-2 col-sm-offset-7">
                                    <button type="button" id="button-save" class="btn-primary btn-lg btn btn-block" onclick="save({{ $id_ajuste }})">Guardar</button>
                                </div>
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
    <script src="js/plugins/JQueryUI/combobox-autocomplete.js">  </script>
    <script src="js/item_crear.js"></script>
@stop
