@extends('layouts.app')

@section('content')

<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Adendas a Presupuestos</h5>
                        <li class="dropdown">
                            <button class="dropdown-toggle btn-primary btn" data-toggle="dropdown" aria-expanded="false">Acción <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                @permission('crear.adenda')
                                <li><a href="{{ url('/adenda_crear') }}">Crear</a></li>
                                @endpermission
                                <li><a onclick="exportarActual()">Exportar</a></li>
                                <li><a onclick="exportar()">Listado de Ajustes</a></li>
                            </ul>
                        </li>
                    </div>
                    <div class="ibox-content">
                        <div class="row" id="app">
                            <form id="filtros" class="m-t" role="form" method="POST" action="{{ url('/home') }}">
                                {{ csrf_field() }}
                                <div class="form-group col-sm-3">
                                    <label for="inputCentro" class="btn-block">Nombre centro</label>
                                    <input id="centro" name="centro" class="filtro form-control" type="text" placeholder="Ingrese el nombre del centro"   v-model="input1">
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="inputCuenta" class="btn-block">Estado de la adenda</label>
                                    <select id="estado" name="estado" class="filtro form-control"  required>
                                        <option value="todos"selected>Todos</option>
                                        @include('partials/adendas/select-estado')
                                    </select>
                                </div>    
                                
                                <div class="form-group col-sm-2" id="data_1">
                                    <label for="inputActualizacion" class="btn-block">Fecha desde</label>
                                    <div class=" input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input id="fechai" name="fechai" v-model="input2" class="filtro form-control" placeholder="Ingrese la Fecha" type="text">
                                    </div>
                                    <label for="inputActualizacion" class="btn-block">Fecha Hasta</label>
                                    <div class=" input-group date" id="inputActualizacion" >
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input id="fechaf" name="fechaf" v-model="input2" class="filtro form-control" placeholder="Ingrese la Fecha" type="text">
                                    </div>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="numeroFilter" class="btn-block">Numero de documento</label>
                                    <input id="numero" name="numero" class="filtro form-control" type="text" placeholder="Ingrese el numero de documento"  >
                                </div>
                                <div class="form-group col-sm-2">
                                    <button id="filtrar" type="button" class="btn btn-w-m btn-block btn-gray" >Filtrar</button>
                                    <button type="button" class="btn btn-w-m btn-block btn-gray" >Limpiar</button>
                                </div>
                            </form>
                        </div>
                        <div>
                        <table id="adendas" class="display" cellspacing="0" width="100%">
						<thead>
                            <tr>
                                <th>Proyecto</th>
                                <th>No. de documento</th>
                                <th>No. de Ajustes</th>
                                <th>Fecha de documento</th> 
                                <th>Descripción</th>
                                <th>Costo</th>
                                <th>Utilidad</th>
                                <th>Admin.</th>
                                <th>Subtotal</th>
                                <th>ITBMS</th>
                                <th>Total</th>
                                <th>acciones</th>
							</tr>
						 	<tr style="font-size:10px">
                                <th></th>
                                <th></th>
                                <th> </th>
                                <th></th> 
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
							</tr>
                        </thead>

                            <tfoot>
                                <tr>
                                   <th></th>
							<th></th>
							<th></th> 
							<th></th>
							 <th></th>
                             <th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
                                </tr>
                            </tfoot>
					{{--  		<th>Estado</th>
							<th>Opciones</th>    --}}
						</table>

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
		$(document).ready(function() {
			var table = $('#adendas').DataTable( {
                aLengthMenu: [
                    [10, 25, 50, 100, 200, -1],
                    [10,25, 50, 100, 200, "Todos"]
                ],
                iDisplayLength: 10,
				"processing": true,
				"serverSide": true,
                    "ajax": {
                        "url":"{{route('adendas.list')}}",
                        "type":"POST",
                        "data":
                        function ( d ) {    
                            d = $.extend({},d,{"_token": "{{ csrf_token() }}"});
                            $(".filtro").each(function(index){
                                var jsonData = {};
                                jsonData[this.id] =$(this).val();
                                d = $.extend( {}, d,jsonData);
                            }); 
                            
                            return d;
                        }
                    },
				"columns":[
					{data:'centro'},
					{data:'numero', render:function(data,type,row){
                        return '<a href=\"{{route('adenda.detail')}}?idAdenda='+row['id']+'\">'+data+'</a>'
                    }},
                    {data:'linkajustes'},
					{data:'fechadoc'},
					{data:'descripcion'},
					{ data: 'costo', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
					{data:'utilidad', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
					{data:'admin', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
					{data:'subtotal', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
					{data:'itbms', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
					{data:'total', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ) },
                    {data: 'accion', targets: 'no-sort', orderable: false}

				]
				,
				 "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    var values = [5,6,7,8,9,10];
                    for(var x=0; x<values.length;x++){
                        // Remove the formatting to get integer data for summation
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };
            
                        // Total over all pages
                        total = api
                            .column(values[x] )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
            
                        // Total over this page
                        pageTotal = api
                            .column(values[x], { page: 'current'} )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
            
                        // Update footer
                        $( api.column( values[x]).header() ).html(
                            '$'+Math.round(pageTotal*100)/100
                        );
                    }
                }
			} );

            $("#filtrar").on('click',function(){
                table.ajax.reload();
            });
		} );
        function exportar(){
            var cent=0;
            var est=0;
            var feci=0;
            var fecf=0;
            var num=0;
            var di='';
            var df='';
            var centro=$('#centro').val();
            var estado=$('#estado').val();
            var fechai=$('#fechai').val();
            var fechaf=$('#fechaf').val();
            var numero=$('#numero').val();
            if(!centro) cent=1;
            if(!fechai) feci=1;
            if(estado=='todos') est=1;
            if(!fechaf) fecf=1;
            if(!numero) num=1; 
            var patron=cent+','+est+','+feci+','+fecf+','+num;     
            var filtro=0;
            switch(patron) {
                case '1,1,1,1,0':
                    filtro=1;
                    break;
                case '1,1,1,0,0':
                    filtro=1;
                    break;
                case '1,1,0,0,0':
                    di=fechai.split("/");
                    df=fechaf.split("/");
                    fechai=di[2]+'-'+di[0]+'-'+di[1]+' 00:00:00';
                    fechaf=df[2]+'-'+df[0]+'-'+df[1]+' 00:00:00';
                    filtro=2;
                    break;
                case '1,0,0,0,0':
                    di=fechai.split("/");
                    df=fechaf.split("/");
                    fechai=di[2]+'-'+di[0]+'-'+di[1]+' 00:00:00';
                    fechaf=df[2]+'-'+df[0]+'-'+df[1]+' 00:00:00';                    
                    filtro=3;
                    break;
                case '0,0,0,0,0':
                    di=fechai.split("/");
                    df=fechaf.split("/");
                    fechai=di[2]+'-'+di[0]+'-'+di[1]+' 00:00:00';
                    fechaf=df[2]+'-'+df[0]+'-'+df[1]+' 00:00:00';                    
                    filtro=4;
                    break;
                case '0,1,1,1,1':
                    filtro=5;
                    break;
                case '0,0,1,1,1':
                    filtro=6;
                    break;
                case '0,0,0,1,1':
                    filtro=6;
                    break;
                case '0,0,0,0,1':
                    di=fechai.split("/");
                    df=fechaf.split("/");
                    fechai=di[2]+'-'+di[0]+'-'+di[1]+' 00:00:00';
                    fechaf=df[2]+'-'+df[0]+'-'+df[1]+' 00:00:00';                    

                    filtro=7;
                    break;
                case '1,0,1,1,1':
                    filtro=8;
                    break;
                case '1,0,1,1,0':
                    filtro=9;
                    break;
                case '1,1,0,0,1':
                    di=fechai.split("/");
                    df=fechaf.split("/");
                    fechai=di[2]+'-'+di[0]+'-'+di[1]+' 00:00:00';
                    fechaf=df[2]+'-'+df[0]+'-'+df[1]+' 00:00:00';                    
                    filtro=10;
                    break;
                default:
                    filtro=0;
            }                           
            var url = subfolder+ "/export_home_adendas?centro="+centro+'&estado='+estado+'&fechai='+fechai+'&fechaf='+fechaf+'&numero='+numero+'&filtro='+filtro;              
            $(location).attr('href',url);             
        }
        function exportarActual(){
            var url = "{{URL::to('/exportar_actual_adendas')}}?";
            $(".filtro").each(function(index){
                url+=this.id+"="+$(this).val()+"&";
            }); 
            $(location).attr('href',url);
        }
    </script>
@stop
