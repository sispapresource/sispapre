<button onclick="sendData(this)" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-form-{{$propuesta->id}}">Opciones</button>

<!-- modal-->
<div id="modal-form-{{$propuesta->id}}" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3>Opciones: <span id="nameProject" style="color:#337ab7"></span></h3>
            </div>
            <div class="modal-body">
                <input type="text" name="elementID" id="elementID" value="" hidden/>
                <input type="text" name="elementID2" id="elementID2" value="" hidden/>

                <div class="row">
                    <div class="col-sm-12 b-r"> 
                        <a id="detailsLinkModal" href="{!! route('propuestas.show',[$propuesta->id]) !!}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Ver Detalle
                        </a>
                        <a id="detailsLinkModal" href="{!! route('createversion',[$propuesta->id]) !!}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Subir documentos
                        </a>
                        @if($propuesta->ultimaVersion()!=null)
                        <a id="detailsLinkModal" href="{!! route('estado',[$propuesta->ultimaVersion()->id]) !!}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Cambiar estados
                        </a>
                        @endif
                        
                        <a id="detailsLinkModal" href="#" class="btn btn-lg btn-primary btn-block btn-modal">
                            Imprimir
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /modal -->