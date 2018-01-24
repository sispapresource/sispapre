<div class="row">

    <div class="col-md-12">


        <!-- No propuesta Field -->
        <div class="form-group col-sm-4">
            {!! Form::label('Cliente', 'Cliente') !!}<br>
            {{$propuesta->cuenta->nombre_centro}}
        </div>
        <div class="form-group col-sm-4">
            {!! Form::label('Linea_de_venta', 'Linea de venta') !!}<br>
            {{$propuesta->lineaDeVenta->nombre}}
        </div>
        <div class="form-group col-sm-4">
            {!! Form::label('Usuario', 'Usuario') !!}<br>
            {{$propuesta->usuario->name}}

        </div>
        <div class="form-group col-md-12">
            <div class="upload-div" style="padding-left:80px;">
                <input name="document" type="file" class="btn btn-gray col-sm-6 col-md-offset-3 b-r" />
            </div>
        </div>

        <div class="form-group col-sm-4" id="data_1">

            <label for="inputActualizacion" class="btn-block">Fecha de expiración o revisión</label>
            <div class="input-group date" id="inputActualizacion">
                <span class="input-group-addon">
                    <i class="fa fa-calendar">
                    </i>
                </span>
                <input id="fecha" name="fecha" class="form-control" placeholder="Ingrese la Fecha" type="text">
            </div>
        </div>

    </div>

</div>

<script>
    function cambiar(){
        $("#lineaSel").toggle();
        $("#lineaCreate").toggle();
    }

</script>