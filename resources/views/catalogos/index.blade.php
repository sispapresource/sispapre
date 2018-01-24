@extends('layouts.app')

@section('content')


<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Catalogos</h5>


                    </div>
                    <!-- Main content -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Nav tabs -->
                            <div class="card">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a  style="color: white!important;" href="#proyectos" aria-controls="home" role="tab" data-toggle="tab">
                                            Tipos de Proyectos
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a style="color: white!important;" href="#documentos" aria-controls="profile" role="tab" data-toggle="tab">
                                            Ducumentación de Proyectos
                                        </a>
                                    </li>

                                    <li role="presentation">
                                        <a style="color: white!important;" href="#clientes" aria-controls="profile" role="tab" data-toggle="tab">
                                            Clientes
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    @include('catalogos.tipoproyecto.tab')

                                    @include('catalogos.documentos.tab')

                                    @include('catalogos.clientes.tab')
                                </div>



                            </div>
                        </div>
                    </div>
                    <!-- /Main content -->

                </div>
            </div>


        </div>
    </div>
</div>
@endsection('content')