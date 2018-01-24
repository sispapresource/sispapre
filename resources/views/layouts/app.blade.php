<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Flexio</title>

        <link rel="stylesheet" href="{{ asset('css/all.css') }}">

{{--  
        <!-- Estos no están siendo utilizados -->
        <link href="{!! asset('css/jquery.dataTables.yadcf.css') !!}" rel="stylesheet" type="text/css" />  --}}

    </head>
    <body id="app-layout" class="top-navigation white-bg" >
        <div id="wrapper">
            <div id="page-wrapper" class="white-bg">
                <div class="row border-bottom white-bg">
                    <nav class="navbar navbar-static-top" role="navigation">
                        <div class="navbar-header">
                            <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                                <i class="fa fa-reorder"></i>
                            </button>
                            <a href="{{ url('/home') }}" class="navbar-brand nav-logo">
                                <img class="img-responsive nav-logo" src="{!! asset('img/flexio_logo.jpg') !!}" ALT="Error cargando imagen" style="display: block;" id="imglogo">
                            </a>
                            @permission('ver.propuestas')
                            <a href="{!! route('propuestas.index') !!}" class="navbar-brand">Propuestas</a>
                            @endpermission
                            @if (!Auth::guest())
                            @permission('ver.proyectos')
                            <a href="{{ url('/home') }}" class="navbar-brand">Proyectos</a>
                            @endpermission


                            @permission('ver.adendas')
                            <a href="{{ url('/home_adendas') }}" class="navbar-brand">Adendas</a>
                            @endpermission
                            @permission('ver.seguridad')
                            <a href="{{ url('/home_seguridad') }}" class="navbar-brand">Seguridad</a>
                            @endpermission
                            <li class="dropdown">
                                <a class="dropdown-toggle btn-primary" data-toggle="dropdown" aria-expanded="false">Administración</a>
                                <ul class="dropdown-menu" role="menu">

                                    @permission('ver.usuarios')
                                    <a href="{{ url('/set_rol') }}" class="navbar-brand">Usuarios</a>
                                    @endpermission
                                    @permission('ver.roles')
                                    <a href="{{ url('/edit_permisos') }}" class="navbar-brand">Roles</a>
                                    @endpermission
                                    @permission('ver.configuraciones')
                                    <a href="{{ route('configuracion.formula.index') }}" class="navbar-brand">Configuración</a>
                                    @endpermission
                                    @permission('ver.trazabilidad')
                                    <a href="{{ url('/verTrazabilidad') }}" class="navbar-brand">Trazabilidad</a>
                                    @endpermission
                                    <a href="{{ url('/upload_data') }}" class="navbar-brand">Cargar Datos</a>
                                    @permission('ver.log_usuarios')
                                    <a href="{{ url('/log_login') }}" class="navbar-brand">Log Usuarios</a>
                                    @endpermission
                                    <a href="{{ url('/catalogos') }}" class="navbar-brand">Catálogos</a>
                                </ul>
                            </li>
                            @endif
                        </div>
                        <div class="navbar-collapse collapse" id="navbar">
                            <ul class="nav navbar-top-links navbar-right">
                                @if (Auth::guest())
                                <li><a href="{{ url('/login') }}">Login</a></li>
                                @else
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        {{ Auth::user()->name }} <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i> Logout</a></li>
                                    </ul>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="modal fade" id="modalCargando" role="dialog" data-backdrop="static" style="background-color: rgba(0,0,0,0.75)">
                    <div class="modal-dialog" style="top:300px">
                        <div align="center">      
                            <img src="{!! asset('img/loading.gif') !!}" width="60" height="60"/>
                        </div>
                        <div align="center">
                            <h4 style="color:#BBBBBB;font-weight:bold">Cargando...</h4> 
                        </div>
                    </div>
                </div>
                @yield('content')
            </div>
        </div>

        <script src="{{ asset('js/all.js') }}"></script>
        <script src="{!! asset('js/scripts.js') !!}"></script>
   {{--       <!-- JavaScripts -->
        
       
        <!-- Estos no están siendo utilizados-->

         
            <!-- Flot -->
        <script src="{!! asset('js/plugins/flot/jquery.flot.js') !!}"></script>
        <script src="{!! asset('js/plugins/flot/jquery.flot.tooltip.min.js') !!}"></script>
        <script src="{!! asset('js/plugins/flot/jquery.flot.resize.js') !!}"></script>


        <script src="{!! asset('js/plugins/slimscroll/jquery.slimscroll.min.js') !!}"></script>

        <!-- JavaScripts Dashboard -->
        <script src="{!! asset('js/plugins/metisMenu/jquery.metisMenu.js') !!}"></script>

        <!-- ChartJS-->
        <script src="{!! asset('js/plugins/chartJs/Chart.min.js') !!}"></script>
        <script src="{!! asset('js/Chart.bundle.js') !!}"></script>
        <script src="{!! asset('js/utils.js') !!}"></script>

        <!-- Custom and plugin javascript -->
        <script src="{!! asset('js/inspinia.js') !!}"></script>
        <script src="{!! asset('js/plugins/pace/pace.min.js') !!}"></script>

        <!-- Peity demo -->
        <script src="{!! asset('js/plugins/peity/jquery.peity.min.js') !!}"></script>
        <script src="{!! asset('js/demo/peity-demo.js') !!}"></script>
        --}}

        @include('sweet::alert')

        <script>
            $(function() {
                $('.chosen-select').chosen();
                $('.chosen-select-deselect').chosen({ allow_single_deselect: true });
            });
        </script>

        {{--  <script src="{!! asset('js/jquery.dataTables.yadcf.js') !!}"></script>  --}}
        @yield('page-script')

    </body>
</html>


