<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-form-{{$adenda->id}}">Opciones</button>
<div id="modal-form-{{$adenda->id}}" class="modal fade" aria-hidden="true">
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
                                            @permission('crear.adenda')
                                            <a href="{{ route('adenda.edit',['idAdenda'=>$adenda->id]) }}" class="btn btn-lg btn-primary btn-block btn-modal">Editar Adenda</a>
                                            @endpermission  
                                            <a href="{{ route('adenda.detail',['idAdenda'=>$adenda->id]) }}" class="btn btn-lg btn-primary btn-block btn-modal">Ver Ajustes</a>
                                            <a href="{{ route('ajuste.create',['idAdenda'=>$adenda->id]) }}" class="btn btn-lg btn-primary btn-block btn-modal">Crear Ajuste</a>
                                            <a href="{{ route('export_adenda',['tipo'=>'excel','idAdenda'=>$adenda->id]) }}" class="btn btn-lg btn-primary btn-block btn-modal" >Exportar adenda en Excel</a>
                                            <a href="{{ route('export_adenda',['tipo'=>'pdf','idAdenda'=>$adenda->id]) }}" class="btn btn-lg btn-primary btn-block btn-modal">Exportar adenda en PDF</a>
                                            <a data-dismiss="modal" data-toggle="modal" data-target="#modal-form-upload-{{$adenda->id}}" class="btn btn-lg btn-primary btn-block btn-modal">Subir Documentos</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                    <div id="modal-form-upload-{{$adenda->id}}" class="modal fade" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <form role="form" method="POST" action="{{ route('adenda.cargar',['idAdenda'=>$adenda->id]) }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h3><span>Subir Documentos</span></h3>
                                    </div>
                                    <div class="modal-body">
                                        <div class="upload-div">
                                            <input name="document" type="file" class="btn btn-gray"/>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="col-sm-6 b-r">
                                            <button type="button" class="btn btn-gray btn-block" data-dismiss="modal">Close</button>
                                        </div>
                                        <div class="col-sm-6 b-r">
                                            <button type="submit" class="btn btn-primary btn-block">Guardar</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>