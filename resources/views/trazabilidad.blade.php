@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content">
    <div class="container container-lg">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins" id="divGastado">
                    <div class="ibox-content">
                            <div id="divFilas">
                            <div class="jqGrid_wrapper">
                                <table id="table_trazable" style="visibility: visible" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Proyecto</th>
                                            <th>Cuenta</th>
                                            <th>Usuario</th>
                                            <th>Cantidad modificada</th>
                                            <th>Cantidad nueva</th>
                                            <th>Fecha y Hora</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($query as $item)
                                        <tr>
                                            <td>
                                                {{ $item->nombre_centro }}
                                            </td>
                                            <td>
                                                {{ $item->id_cuenta }}
                                            </td>
                                            <td>
                                                {{ $item->nombre_usuario }}
                                            </td>
                                            <td>
                                                {{ $item->old_presupuesto }}
                                            </td>
                                            <td>
                                                {{ $item->presupuesto }}
                                            </td>
                                            <td>
                                                {{ $item->fecha }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                                {{ $query->links() }}
                                <div id="pager_list_4"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')


@stop