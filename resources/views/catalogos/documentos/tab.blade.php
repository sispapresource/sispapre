<div role="tabpanel" class="tab-pane" id="documentos">


    <div class="panel panel-default">
        <div class="panel-body text-right">        

            <div class="ibox-title">
                <li class="dropdown">
                    <button class="dropdown-toggle btn-primary btn" data-toggle="dropdown" aria-expanded="false">Acción <span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{route('catalogos.documentos.create')}}">Crear</a></li>
                    </ul>
                </li>
            </div>

        </div>
    </div>
    <div class="ibox-content">

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nombre</th>
                    <th>Tipos Proyectos </th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $pos=1 ?>
                @foreach($documentos as $documento)
                <tr>
                    <th scope="row"><?php print $pos++ ?></th>
                    <td>{{$documento->nombre}}</td>
                    <td>@foreach($documento->tipoproyecto as $tipo)
                        @if($tipo->id!=0)
                        {{$tipo->nombre}}<br>
                        @endif
                        @endforeach
                    </td>
                    <td>
                        <!-- Trigger the modal with a button -->
                        <button type="button" class="open-AddBookDialog btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-documentos-{{$documento->id}}">Opciones</button>

                        <!-- modal-->
                        <div id="modal-documentos-{{$documento->id}}" class="modal fade" aria-hidden="true">
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
                                        <div class="row">
                                            <div class="col-sm-12 b-r"> 
                                                <a id="detailsLinkModal" href="{{route('catalogos.documentos.edit',['documentos'=>$documento->id])}}" class="btn btn-lg btn-primary btn-block btn-modal">
                                                    Editar Tipos De Proyecto
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /modal -->
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$documentos->links()}}
    </div>  
</div>

