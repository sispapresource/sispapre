@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container container-lg">
        <div class="row">
            <div class="col-lg-12">
                <div style="width: 75%">
                    <canvas id="canvas"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')

    <script>
        var chartData = {
            labels: ["01-02", "01-03", "01-04", "01-05", "01-06", "01-07", "01-08"],
            datasets: [{
                type: 'bar',
                label: 'Linea Base',
                backgroundColor: window.chartColors.red,
                yAxisID: "y-axis-1",
                data: [
                    500, 
                    550, 
                    525, 
                    400, 
                    550, 
                    560, 
                    490
                ],
                borderColor: 'white',
                borderWidth: 2
            }, 
            {
                type: 'bar',
                label: 'Ajustado',
                backgroundColor: window.chartColors.yellow,
                borderWidth: 2,
                fill: false,
                yAxisID: "y-axis-1",
                data: [
                    300, 
                    450, 
                    425, 
                    300, 
                    350, 
                    360, 
                    390
                ]
            },
            {
                type: 'bar',
                label: 'Desembolsado',
                backgroundColor: window.chartColors.orange,
                borderWidth: 2,
                fill: false,
                yAxisID: "y-axis-1",
                data: [
                    200, 
                    350, 
                    325, 
                    300
                ]
            },
            {
                type: 'bar',
                label: 'Desembolsado+Pendiente',
                backgroundColor: window.chartColors.green,
                borderWidth: 2,
                fill: false,
                yAxisID: "y-axis-1",
                data: [
                    600, 
                    650, 
                    425, 
                    500, 
                    750, 
                    860, 
                    490
                ]
            },
            {
                type: 'line',
                label: 'Linea Base',
                borderColor: window.chartColors.red,
                borderWidth: 2,
                fill: false,
                yAxisID: "y-axis-2",
                data: [
                    1500, 
                    1550, 
                    1625, 
                    1700, 
                    1750, 
                    1860, 
                    1990
                ]
            },
            {
                type: 'line',
                label: 'Ajustado',
                borderColor: window.chartColors.yellow,
                borderWidth: 2,
                fill: false,
                yAxisID: "y-axis-2",
                data: [
                    1300, 
                    1450, 
                    1525, 
                    1600, 
                    1750, 
                    1860, 
                    1990
                ]
            },
            {
                type: 'line',
                label: 'Desembolsado',
                borderColor: window.chartColors.orange,
                borderWidth: 2,
                fill: false,
                yAxisID: "y-axis-2",
                data: [
                    1200, 
                    1350, 
                    1425, 
                    1500
                ]
            },
            {
                type: 'line',
                label: 'Desembolsado+Pendiente',
                borderColor: window.chartColors.green,
                borderWidth: 2,
                fill: false,
                yAxisID: "y-axis-2",
                data: [
                    600, 
                    750, 
                    925, 
                    1500, 
                    1750, 
                    1860, 
                    1990
                ]
            }]

        };
        window.onload = function() {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myMixedChart = new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Ejemplo Grafico de Costos'
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: true
                    },
                    scales: {
                        yAxes: [{
                            type: "linear", // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                            display: true,
                            position: "left",
                            scaleLabel: {
                                display: true,
                                labelString: 'Semanas'
                            },
                            id: "y-axis-1",
                        }, {
                            type: "linear", // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                            display: true,
                            position: "right",
                            scaleLabel: {
                                display: true,
                                labelString: 'Aumulado'
                            },
                            id: "y-axis-2",
                            gridLines: {
                                drawOnChartArea: false
                            }
                        }],
                    }



                }
            });
        };

        document.getElementById('randomizeData').addEventListener('click', function() {
            chartData.datasets.forEach(function(dataset) {
                dataset.data = dataset.data.map(function() {
                    return randomScalingFactor();
                });
            });
            window.myMixedChart.update();
        });
    </script>

@stop