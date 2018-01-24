<!-- Trigger the modal with a button -->
<!--<button type="button" class="open-AddBookDialog btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-form-">Opciones</button>-->
<!-- modal options proyectos -->
<div id="modal-form-centros" class="modal fade" aria-hidden="true">
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
                        <a id="detailsFinanciera" href="" class="btn btn-lg btn-primary btn-block btn-modal">
                            Análisis de ejecución financiera
                        </a>
                        @permission('ver.cantidades')
                        <a id="detailsConsumo" href="" class="btn btn-lg btn-primary btn-block btn-modal">
                            Análisis por cantidades consumidas
                        </a>
                        @endpermission

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /modal -->