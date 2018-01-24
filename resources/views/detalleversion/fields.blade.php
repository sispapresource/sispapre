<div class="row">

    <div class="col-sm-7">

        <div>
            <div class="form-group col-sm-6">
                <div class="checkbox">
                    <label><input type="checkbox" id="crearitem" name="crearitem" onclick="cambiar('item')">Crear nuevo item?</label>
                </div>
            </div>

            <!-- Linea Venta Select -->
            <div id="itemSelect" class="form-group col-sm-6">
                {!! Form::label('item', 'Seleccione un item:') !!}
                {!! Form::select('item', $itemprop, null, ['class' => 'form-control','placeholder'=>'seleccione...']) !!}
            </div>

            <!-- Linea venta create -->
            <div id="itemCreate" class="form-group col-sm-6" hidden>
                {!! Form::label('item_create', 'Nombre item:') !!}
                {!! Form::text('item_create', null, ['class' => 'form-control']) !!}
            </div>

        </div>

        <div>
            <div class="form-group col-sm-6">
                <div class="checkbox">
                    <label><input type="checkbox" id="crearunidad" name="crearunidad" onclick="cambiar('unidad')">Crear nueva unidad?</label>
                </div>
            </div>

            <!-- Linea Venta Select -->
            <div id="unidadSelect" class="form-group col-sm-6">
                {!! Form::label('unidad', 'Seleccione una unidad:') !!}
                {!! Form::select('unidad', $unidad, null, ['class' => 'form-control','placeholder'=>'seleccione...']) !!}
            </div>

            <!-- Linea venta create -->
            <div id="unidadCreate" class="form-group col-sm-6" hidden>
                {!! Form::label('unidad_create', 'Nombre unidad:') !!}
                {!! Form::text('unidad_create', null, ['class' => 'form-control']) !!}
            </div>

        </div>
        <!-- Cantidad Field -->

        <div class="form-group col-sm-6"></div>
        <div class="form-group col-sm-6">
            {!! Form::label('losa', 'Losa:') !!}
            {!! Form::number('losa', null, ['class' => 'form-control']) !!}
        </div>

        <!-- Cantidad Field -->

        <div class="form-group col-sm-6"></div>
        <div class="form-group col-sm-6">
            {!! Form::label('cantidad', 'Cantidad:') !!}
            {!! Form::number('cantidad', null, ['class' => 'form-control']) !!}
        </div>

        <!-- Precio Unitario Field -->
        <div class="form-group col-sm-6"></div>
        <div class="form-group col-sm-6">
            {!! Form::label('preciou', 'Precio Unitario:') !!}
            {!! Form::number('preciou', null, ['class' => 'form-control']) !!}
        </div>

        <!-- %total Field -->
        <div class="form-group col-sm-6"></div>
        <div class="form-group col-sm-6">
            {!! Form::label('porcentaje', '% Total:') !!}
            {!! Form::number('porcentaje', null, ['class' => 'form-control']) !!}
        </div>

        <!-- %cuenta Field -->
        <div class="form-group col-sm-6"></div>
        <div class="form-group col-sm-6">
            {!! Form::label('cuenta', 'Seleccione una cuenta:') !!}
            {!! Form::select('cuenta', $cuentas, null, ['class' => 'form-control','placeholder'=>'seleccione...']) !!}
        </div>
    </div>

</div>

<script>
    function cambiar(sel){
        $("#"+sel+"Select").toggle();
        $("#"+sel+"Create").toggle();
    }

</script>