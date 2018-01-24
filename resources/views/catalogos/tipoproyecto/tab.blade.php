<div role="tabpanel" class="tab-pane active" id="proyectos">

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="ibox-title">
                <li class="dropdown">
                    <button class="dropdown-toggle btn-primary btn" data-toggle="dropdown" aria-expanded="false">Acción <span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{route('catalogos.proyecto.create')}}">Crear</a></li>
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
                    <th>Descripcion</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $pos=1 ?>
                @foreach($tipos as $tipo)
                @if($tipo->id!=0)
                <tr>
                    <th scope="row"><?php print $pos++ ?></th>
                    <td>{{$tipo->nombre}}</td>
                    <td>{{$tipo->descripcion}}</td>
                    <td>
                        <!-- Trigger the modal with a button -->
                        <button type="button" class="open-AddBookDialog btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-catalogo-{{$tipo->id}}">Opciones</button>
                        <!-- modal-->
                        <div id="modal-catalogo-{{$tipo->id}}" class="modal fade" aria-hidden="true">
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
                                                <a id="detailsLinkModal" href="{{route('catalogos.proyecto.edit',['proyecto'=>$tipo->id])}}" class="btn btn-lg btn-primary btn-block btn-modal">
                                                    Editar
                                                </a>
                                                <form method="POST" action="{{route('catalogos.proyecto.destroy',['proyecto'=>$tipo->id])}}">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <button type="submit" class="btn btn-lg btn-primary btn-block btn-modal">
                                                        Eliminar
                                                    </button>
                                                </form>
                                                @permission('ver.presupuesto')
                                                @endpermission
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /modal -->

                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        {{$tipos->links()}}
    </div>
</div>


