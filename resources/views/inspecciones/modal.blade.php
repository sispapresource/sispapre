<button onclick="sendData(this)" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-form-{{$centro->id_centro}}">Opciones</button>

<!-- modal-->
<div id="modal-form-{{$centro->id_centro}}" class="modal fade" aria-hidden="true">
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
                        @permission('ver.incidencias')
                        <a id="verInspeccionesLinkModal" href="{{ url('/home_evaluaciones?idCentro='.$centro->id_centro) }}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Ver evaluaciones
                        </a>
                        @endpermission
                        @permission('ver.hallazgos')
                        <a id="verHallazgosLinkModal" href="{{ url('/home_hallazgos?idCentro='.$centro->id_centro) }}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Ver hallazgos
                        </a>
                        @endpermission
                        <a id="crearInspeccionLinkModal" href="{{ url('/evaluacion_crear?idCentro='.$centro->id_centro) }}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Crear evaluación
                        </a> 
                        <a id="crearHallazgoLinkModal" href="{{ url('/hallazgo_crear?idCentro='.$centro->id_centro) }}" class="btn btn-lg btn-primary btn-block btn-modal">
                            Crear hallazgo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /modal -->
