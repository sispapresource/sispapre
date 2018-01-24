<div role="tabpanel" class="tab-pane" id="clientes">


    <div class="panel panel-default">
        <div class="panel-body text-right">        

            <div class="ibox-title">
                <li class="dropdown">
                    <button class="dropdown-toggle btn-primary btn" data-toggle="dropdown" aria-expanded="false">Acción <span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{route('catalogos.cliente.create')}}">Crear</a></li>
                    </ul>
                </li>
            </div>

        </div>
    </div>
    <div class="ibox-content">

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Direccion</th>
                    <th>Teléfono</th>
                    <th>Nombre Contacto</th>
                    <th>Teléfono Contacto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $cliente)
                <tr>
                    <td>{{$cliente->nombre}}</td>
                    <td>{{$cliente->direccion}}</td>
                    <td>{{$cliente->telefono}}</td>
                    <td>{{$cliente->nombre_contacto}}</td>
                    <td>{{$cliente->telefono_contacto}}</td>
                    <td>
                        <!-- Trigger the modal with a button -->
                        <button type="button" class="open-AddBookDialog btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-cliente-{{$cliente->id}}">Opciones</button>

                        <!-- modal-->
                        <div id="modal-cliente-{{$cliente->id}}" class="modal fade" aria-hidden="true">
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
                                            <!--<div class="col-sm-12 b-r"> 
                                                <a id="detailsLinkModal" href="" class="btn btn-lg btn-primary btn-block btn-modal">
                                                </a>
                                            </div>-->
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
        {{$clientes->links()}}
    </div>  
</div>

