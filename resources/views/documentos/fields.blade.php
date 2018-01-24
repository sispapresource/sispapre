<div class="row">
    <div class="form-group col-sm-4">
        <label for="nombre">Nombre </label>
        <input type="text" class="form-control" id="nombre" name="nombre">
    </div>
    <div class="clearfix"></div>
    @if($idCentro==null)
    <div class="form-check col-sm-4">
        <label class="form-check-label">
            <input id="requerido" name="requerido" type="checkbox" class="form-check-input">
            Requerido
        </label>
    </div>
    @endif
</div>