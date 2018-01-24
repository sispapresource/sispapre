<div class="row">

    <div class="col-sm-7">


        <div class="form-group col-sm-6">
            <!--<div class="checkbox">
<label><input type="checkbox" id="crearcliente" name="crearcliente" onclick="cambiar('cliente')">Crear nuevo cliente?</label>
</div>-->
            <a href="{{route('catalogos.cliente.create',['callback'=>true])}}" class="btn btn-primary">Crear cliente</a>
        </div>

        <!-- Cliente select field -->
        <div class="form-group col-sm-6 cliente">
            {{ Form::label('Cliente', 'Cliente: ') }}
            {{ Form::select('Cliente', $clientes, null, ['name'=>'clientes[]','multiple','data-placeholder'=>'seleccione los clientes' , 'class' => 'form-control chosen-select multiple']) }}
        </div>

        <!-- No propuesta Field -->
        <div class="form-group col-sm-6">
            {{ Form::label('No_De_Propuesta', 'No. de propuesta:') }}
            {{ Form::text('No_De_Propuesta', null, ['class' => 'form-control']) }}
        </div>
        <div class="clearfix"></div>
        <div class="form-group col-sm-6">
            <div class="checkbox">
                <label><input type="checkbox" id="crearcentro" name="crearcentro" onclick="cambiar('centro')">Crear nuevo proyecto?</label>
            </div>
        </div>

        <!-- Centro Field -->
        <div class="form-group col-sm-6 centro">
            {{ Form::label('Centro_Contable', 'Centro Contable:') }}
            {{ Form::select('Centro_Contable', $centros, null, ['class' => 'form-control','placeholder'=>'seleccione...']) }}
        </div>

        <!-- Centro contable create Field -->
        <div class="form-group col-sm-6 centro" hidden>
            {{ Form::label('Centro_Contable_create', 'Nombre:') }}
            {{ Form::text('Centro_Contable_create', null, ['class' => 'form-control']) }}
        </div>


        <div class="form-group col-sm-6">
            <div class="checkbox">
                <label><input type="checkbox" id="crearlinea" name="crearlinea" onclick="cambiar('linea')">Crear nueva linea?</label>
            </div>
        </div>

        <!-- Linea Venta Field -->
        <div class="form-group col-sm-6 linea">
            {{ Form::label('Linea_De_Venta', 'Linea de Venta:') }}
            {{ Form::select('Linea_De_Venta', $lineaventa, null, ['class' => 'form-control','placeholder'=>'seleccione...']) }}
        </div>

        <!-- No propuesta Field -->
        <div class="form-group col-sm-6 linea" hidden>
            {{ Form::label('crear_linea', 'Linea de Venta:') }}
            {{ Form::text('crear_linea', null, ['class' => 'form-control']) }}
        </div>


    </div>

</div>

<script>
    function cambiar(sel){
        $("."+sel).toggle();
    }
</script>