@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Item {{ $numero_item }} - Ajuste {{ $numero_ajuste }} - Adenda {{ $numero_adenda }} - {{ $nombre_centro }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">       
                            <div class="form-group col-sm-4">
                                <label for="inputActualizacion" class="btn-block">Número de item</label>
                                    <input class="form-control" value="{{ $numero_item }}" type="text" disabled="disabled" class="form-control">
                            </div>
                            <div class="col-sm-4 form-group"> 
                                <label for="inputCuenta" class="btn-block">Creado por</label>
                                <input type="text" value="{{ $usuario }}" id="inputCuenta" class="form-control" disabled="disabled">
                            </div> 
                        </div>
                    </div>   
                    <div class="ibox-sub-title">
                        <h5>Modificaciones</h5>
                    </div>    
                    <div class="ibox-sub-content">
                        @foreach ($items_detalle as $item)                                
                        <div class="row">       
                            <div class="form-group col-sm-3">
                                <label for="inputCuenta" class="btn-block">Cuenta</label>
                                <select name="select" class="form-control" id="select-table" disabled="disabled">
                                    <option value="contacts">{{ $item['nombre_cuenta'] }}</option> 
                                </select>
                            </div>
                            <div class="form-group col-sm-3" id="data_1">
                                <label for="inputActualizacion" class="btn-block">Monto actual</label>
                                <div class="input-group">
                                    <span class="input-group-addon usd-off"><i class="fa fa-usd"></i></span>
                                    <input class="form-control" value="{{ $item['monto_actual'] }}" type="text" disabled="disabled">
                                </div>
                            </div>
                            <div class="col-sm-3 form-group"> 
                                <label for="inputCuenta" class="btn-block">Costo</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                    <input class="form-control" value="{{ $item['monto_modificado'] }}" type="text" disabled="disabled">
                                </div>  
                            </div>
                        </div>   
                        @endforeach
                        <div class="row button-top">       
                            <div class="col-sm-offset-3 form-group col-sm-3 label-horizontal">
                                <label>Costo total:</label>
                            </div>
                            <div class="col-sm-3"> 
                               <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                    <input class="form-control" value="{{ $monto }}" type="text" disabled="disabled">
                                </div>
                            </div>
                        </div>
                        <div class="row">       
                            <div class="form-group col-sm-6">
                                <label for="inputActualizacion" class="btn-block">Observaciones</label>
                                <textarea style="resize: none;" name="observaciones" rows="3" id="observciones" class="form-control" disabled="disabled">{{ $observaciones }}</textarea>
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
    <script src="js/item_detail.js"></script>
@stop