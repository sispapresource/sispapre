@extends('layouts.app')

@section('content')


<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i>Detalle propuesta</h5>
                    </div>
                    <!-- Main content -->
                    <div class="row">
                        <div class="col-md-2">
                            <a  href="{!! route('propuestas.show',[$versiones->id_propuesta]) !!}"  class="btn btn-default">Regresar</a>
                        </div>

                        <div class="col-md-10 col-md-offset-1">
                            @foreach($categorias as $categoria)
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample-{{$categoria['id']}}" aria-expanded="false" aria-controls="collapseExample">
                                            </button>
                                        </div>
                                        <div class="col-md-8">
                                            {{$categoria['nombre']}}
                                        </div>
                                        <div class="col-md-2">
                                            <h3><label>Total:</label>
                                                ${{number_format($categoria['total'],2)}}</h3>

                                        </div>
                                        <div class="col-md-1">
                                            <a href="{!! route('versiones.detalle.create',[$versiones->id,'categoria'=>$categoria['id']]) !!}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                        </div>
                                    </div>

                                    <div class="collapse col-md-12" id="collapseExample-{{$categoria['id']}}">
                                        <div class="card card-block">

                                            @foreach($categoria['detalles'] as $detalle)
                                            <h1> Losa {{$detalle['losa']}}:</h1>
                                            <table class="table table-hover" >
                                                <thead>
                                                    <tr>
                                                        <th>Actividad</th>
                                                        <th>Cantidad</th>
                                                        <th>Unidad</th>
                                                        <th>P.U.</th>
                                                        <th>Total</th>
                                                        <th>%Total</th>
                                                        <th>Cuenta</th>

                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($detalle['detalles'] as $detalle)
                                                    <tr>
                                                        <th scope="row">{{App\ItemPropuesta::find($detalle->id_item)->nombre}}</th>
                                                        <td>{{$detalle->cantidad}}</td>
                                                        <td>{{App\UnidadItem::find($detalle->id_unidad)->nombre}}</td>
                                                        <td>${{number_format($detalle->precio_unitario,2)}}</td>
                                                        <td>${{number_format($detalle->total(),2)}}</td>
                                                        <td>{{round($detalle->porcentaje_total*100)}}%</td>
                                                        <td>{{App\Cuenta::find($detalle->id_cuenta)->id_cuenta}}</td>
                                                        <td>
                                                            {{Form::open(array('route'=>array('versiones.detalle.destroy',$versiones->id,$detalle->id),'method' => 'delete'))}}
                                                            <button type="submit" class="btn"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                                            {{Form::close()}}

                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>


                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @endforeach
                            <div class="col-md-12 text-right">

                                <h3>Presupuesto Total:</h3>
                                <h3>${{number_format($granTotal,2)}}</h3>
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

