<!-- Trigger the modal with a button -->
<button type="button" class="open-AddBookDialog btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-form-{{$documento->id}}">Opciones</button>
<!-- modal-->
<div id="modal-form-{{$documento->id}}" class="modal fade" aria-hidden="true">
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
                        <a id="detailsLinkModal" href="/{{$documento->id}}/{{$centro->id_centro}}/cargar" class="btn btn-lg btn-primary btn-block btn-modal">
                            Cargar documento
                        </a>
                        @if($documento->pivot->url!=null)
                        <a id="detailsLinkModal" href="{{$documento->id}}/{{$centro->id_centro}}/descargar" class="btn btn-lg btn-primary btn-block btn-modal">
                            Descargar documento
                        </a>
                        <a id="detailsLinkModal" href="{{$documento->id}}/{{$centro->id_centro}}/docestado" class="btn btn-lg btn-primary btn-block btn-modal">
                            Cambiar estado
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /modal -->