<button onclick="sendData(this)" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-form-{{$version->id}}">Opciones</button>

<!-- modal-->
<div id="modal-form-{{$version->id}}" class="modal fade" aria-hidden="true">
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
                        <a id="detailsLinkModal" href="{!! route('versiones.detalle.index',[$version->id]) !!}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Ver Detalle
                        </a>
                        <a id="detailsLinkModal" href="{!! route('cartapropuesta',['id'=>$version->id]) !!}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Exportar carta de propuesta
                        </a>
                        <a id="detailsLinkModal" href="{!! route('versiones.edit',[$version->id]) !!}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Actualizar propuesta
                        </a>
                        <a id="detailsLinkModal" href="{!! route('estado',[$version->id]) !!}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Cambiar estado
                        </a>
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