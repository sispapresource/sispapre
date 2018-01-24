@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container container-lg">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><i class="fa fa-calculator"></i> Dashboard gráfico - Proyecto {{ $nombre_centro }} </h5>
                        <input type="hidden" id="idCentro" class="form-control" value="{{ $id_centro }}">
                    </div>
                    <div class="ibox-content">
                        <div class="box-body">
                            <div class="col-sm-12">
                                <div id="areaChart">
                                    <canvas id="canvas" height="280" width="600"></canvas>
                                </div>
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

<script>
    var url = subfolder + '/data_grafico/' + $('#idCentro').val();

    $.get( url , function( dataServer ) {

        var a_labels = [];
        var p_total = [];
        var p_avance = [];
        var gastado = [];

        $.each(dataServer.cuentas, function( index, value ) {
            a_labels.push(value.nombre_cuenta);
        });
        $.each(dataServer.presupuesto, function( index, value ) {
            p_total.push(value.presupuesto_original);
        });
        $.each(dataServer.p_avance, function( index, value ) {
            p_avance.push(value.presupuesto_x_avance);
        });
        $.each(dataServer.gastado, function( index, value ) {
            gastado.push(value.gastado_total);
        });

        var barChartData = {
            labels: a_labels,
            datasets: [{
                label: 'Presupuesto Total',
                backgroundColor: "rgba(220,220,220,0.5)",
                data: p_total
            }, {
                label: 'Presupuesto por avance',
                backgroundColor: "rgba(28,27,25,0.5)",
                data: p_avance
            }, {
                label: 'Gastado',
                backgroundColor: "rgba(151,187,205,0.5)",
                data: gastado
            }]
        };

        var ctx = document.getElementById("canvas").getContext("2d");

        var myPieChart = new Chart(ctx, {
          type: 'bar',
          data: barChartData,
          options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: 'rgb(51,122,183)',
                        borderSkipped: 'bottom'
                    }
                },
                responsive: true,
                title: {
                    display: true,
                    text: 'Costos por Division'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            userCallback: function(value, index, values) {
                                value = value.toString();
                                value = value.split(/(?=(?:...)*$)/);
                                value = value.join('.');
                                return '$' + value+'.00';
                            }
                        }
                    }]
                }
            }
        });
    });
</script>

@stop