<!-- Trigger the modal with a button -->
<button type="button" class="open-AddBookDialog btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-form-{{$key}}">Opciones</button>
<!-- modal options proyectos -->
<div id="modal-form-{{$key}}" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3>Opciones: <span id="nameProject" style="color:#337ab7"></span></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 b-r"> 
                        <a id="detailsFinanciera" href="{{route('presupuestoitems.show',['presupuestoitems'=>$key]).'?idCentro='.$centro->id_centro}}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Detalles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /modal -->