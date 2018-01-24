@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Documentos de {{$centro->nombre_centro}}</h5>
                    </div>
                    <div class="ibox-content">
                        <!--

                        <div class="row" id="app">
                            <form method="POST" action="{{route('documentos.index',['idCentro'=>$centro->id_centro])}}">
                                {{ csrf_field() }}
                                <div class="form-group col-sm-3">
                                    <label for="inputCuenta" class="btn-block">Nombre</label>
                                    <input type="text" name="nombre" placeholder="Ingrese el nombre del documento" id="nombre" class="form-control">
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group col-sm-6" id="data_1">
                                        <label for="inputActualizacion" class="btn-block">Inicio</label>
                                        <div class="input-group date" id="inputActualizacion" >
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input id="inicio" name="inicio" class="form-control" placeholder="Ingrese la Fecha" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6" id="data_1">
                                        <label for="inputActualizacion" class="btn-block">Fin</label>
                                        <div class="input-group date" id="inputActualizacion" >
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input id="fin" name="fin" class="form-control" placeholder="Ingrese la Fecha" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-5">
                                    <button type="submit" class="btn btn-w-m btn-block btn-gray">Filtrar</button>
                                    <form method="POST" action="./documentos?idCentro={{$centro->id_centro}}">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-w-m btn-block btn-gray">Limpiar</button>
                                    </form>
                                </div>
                            </form>


                        </div>

                        -->
                        <div class="ibox-content">
                            <table class="table table-hover" >
                                <thead >
                                    <tr>
                                        <th>Rubro de costo</th>
                                        <th>Presupuesto Original </th>
                                        <th>Adendas</th>
                                        <th>Presupuesto total</th>
                                        <th>Gastado</th>
                                        <th>Consumo Financiero</th>
                                        <th>Consumo unitario</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        @foreach($presupuestoOriginal->groupBy('cuentas.categoria') as $key => $presupuestoitem)
                                    <tr>

                                        <td>
                                          {{ $key }}
                                        </td>

                                        <td>
                                            {{--  {{ $presupuestoitem->sum('total')}}  --}}
                                            ${{ number_format($presupuesto->groupBy('categoria')->get($key)->sum('presupuesto'),2)}}
                                        </td>
                                        <td>
                                            ${{ number_format($adendas->get($key)->sum('monto_nuevo'),2)}}
                                        </td>

                                        <td>
                                            ${{ number_format($presupuesto->groupBy('categoria')->get($key)->sum('presupuesto') + $adendas->get($key)->sum('monto_nuevo') ,2)}}
                                        </td>

                                        <td>
                                            ${{ number_format((null!=$planillas->get($key))? $planillas->get($key)->sum('monto'): 0  + (null!=$facturas->get($key))? $facturas->get($key)->sum('monto'): 0 ,2)}}
                                        </td>

                                        <td>
                                            %{{ number_format( (( (null!=$planillas->get($key))? $planillas->get($key)->sum('monto'): 0  + (null!=$facturas->get($key))? $facturas->get($key)->sum('monto'): 0)*100)/$presupuestoitem->sum('total') ,2)}}
                                        </td>

                                        <td>
                                            %{{ number_format( (( (null!=$planillas->get($key))? $planillas->get($key)->sum('cantidad'): 0  + (null!=$facturas->get($key))? $facturas->get($key)->sum('cantidad'): 0)*100)/$presupuestoitem->sum('cantidad') ,2)}}
                                        </td>

                                        <td>
                                            @include('presupuestoitems.modal')
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>
</div>


@endsection('content')
