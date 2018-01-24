@extends('layouts.app')

@section('content')
<style>
    div[aria-expanded='true']{
        // color: red;
    }

</style>
<div class="wrapper wrapper-content">
    <div class="container container-lg">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><i class="fa fa-calculator"></i> Presupuesto de Proyecto - {{ $centro->nombre_centro }} </h5>
                        <li class="dropdown">
                            <!--<button class="dropdown-toggle btn-primary btn" data-toggle="dropdown" aria-expanded="false">Acción <span class="caret"></span></button>-->
                            <a class="btn btn-primary" href="{{url('/details?idCentro='.$centro->id_centro)}}">Regresar</a>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li> <a href=""></a></li>
                            </ul>
                        </li>
                    </div>
                    <div class="ibox-content">
                        {{Form::open(['method'=>'get','route'=>['presupuestoavance.create']])}}
                        {{Form::token()}}
                        <div class="form-group row">
                            <input name="id_centro" type="text" value="{{$centro->id_centro}}" hidden>
                            <div class="col-sm-4">
                                {{Form::label('codigo', 'Codigo de la cuenta:')}}
                                {{Form::text('codigo', '',['class'=>'form-control'])}}
                            </div>
                            {{Form::submit('Filtrar',['class'=>'btn btn-primary'])}}
                            <a class="btn btn-primary" href="{{ route('presupuestoavance.create',['id_centro'=>$centro->id_centro])}}">Limpiar</a>

                        </div>
                        {{Form::close()}}


                        <div class="row">
                            <div class="col-md-6">

                                @foreach($arbol->groupBy('nivel0') as $key => $cuentanivel0)
                                <div class="panel panel-default" name="{{str_replace('.', '-',$key)}}">
                                    <div class="panel-heading">
                                        <h4 class="panel-title" data-toggle="collapse" data-target=".details-{{str_replace('.', '',$key)}}" >
                                            <a >{{$key}}</a>         
                                        </h4>
                                        <label>Presupuesto: ${{number_format($cuentanivel0->sum('presupuesto'),2)}}</label>
                                    </div>
                                </div>

                                @foreach($cuentanivel0->groupBy('nivel1') as $key2 => $cuentanivel1)
                                <div class="panel panel-collapse collapse details-{{str_replace('.', '', $key)}}" name="{{str_replace('.', '-',$key2)}}">
                                    <div class="panel-heading">
                                        <h4 class="panel-title" data-toggle="collapse" data-target=".details-{{str_replace('.', '',$key2)}}" >
                                            <a >{{$key2}}</a>
                                        </h4>
                                        <label>Presupuesto: ${{number_format($cuentanivel1->sum('presupuesto'),2)}}</label>
                                    </div>
                                </div>
                                @foreach($cuentanivel1 as $cuenta)
                                <div class="col-md-11 col-md-offset-1" style="min-height:0px!important">
                                    <div  class="details-{{str_replace('.', '',$key2)}} panel panel-collapse collapse" name="{{str_replace('.', '-',    $cuenta->id_cuenta)}}">
                                        <div class="panel-body">
                                            <form data-id-presupuesto='{{$cuenta->id}}' class='presupuesto-form'> 
                                                {{csrf_field()}}
                                                <div class="form-group row">
                                                    <div class="col-sm-2">
                                                        <label for="cuenta">Cuenta: {{$cuenta->id_cuenta}}</label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="input-group"> 
                                                            <span class="input-group-addon">$</span>
                                                            <input id="{{$cuenta->id}}" name="presupuesto" type="number" data-initial-value="{{$cuenta->presupuesto}}" value="{{$cuenta->presupuesto}}" min="0" step="0.01" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency presupuesto-input"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <button class="btn btn-default submit-presupuesto" disabled>Guardar cambios</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                @endforeach

                                @endforeach


                            </div>
                            <div class="col-md-4">
                                {{Form::open(['route'=>['presupuestoavance.store','id_centro'=>$centro->id_centro]])}}

                                {{Form::label('presupuesto', 'Presupuesto:')}}
                                {{Form::number('presupuesto', 'value',['class'=>'form-control'])}}

                                {{Form::label('cuenta', 'Cuenta:')}}
                                {{Form::select('cuenta', $cuentas, null,['class'=>'form-control'])}}

                                {{Form::submit('Agregar Cuenta',['class'=>'btn btn-primary'])}}
                                {{Form::close()}}
                                @include('layouts.errors')
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
        $(function(){

            /*
        $("#inputCodigo").bind('input',function(e){
            console.log($(this).val().replace(/\./g,"-"));
            $(".panel:not([name^=\""+$(this).val().replace(/\./g,"-")+"\"])").hide();
            $(".panel:not([name^=\""+$(this).val().replace(/\./g,"-")+"\"])").addClass('show');

            $(".panel[name^=\""+$(this).val().replace(/\./g,"-")+"\"]").show();
            $(".panel[name^=\""+$(this).val().replace(/\./g,"-")+"\"]").removeClass('show');

        });*/

            $(".presupuesto-input").bind('input',function(e){
                console.log("key up en presupuesto input");
                if($(this).val()!= $(this).data('initial-value')){
                    console.log("el valor inicial es diferente del valor actual");
                    $(this).parents('form').find('.submit-presupuesto').prop('disabled',false);
                }else{
                    $(this).parents('form').find('.submit-presupuesto').prop('disabled',true);
                }
            });

            $(".presupuesto-form").submit(function(e){
                e.preventDefault();            
                console.log($(this).data('id-presupuesto'));
                var form = $(this);
                $.ajax({
                    type:'PUT',
                    url: $(this).data('id-presupuesto'),
                    data : $(this).serialize(),
                    success: function( _response ){
                        console.log(this.url);

                        // Handle your response..
                        console.log(_response);
                        swal(
                            'Exito!',
                            'La base de datos ha sido actualizada!',
                            'success'
                        )
                        console.log(form.find('.presupuesto-input').val());
                        form.find('.submit-presupuesto').prop('disabled',true);
                        form.find('.presupuesto-input').data('initial-value',form.find('.presupuesto-input').val());
                        console.log(form.find('.presupuesto-input').data('initial-value'));
                    },
                    error: function( _response ){
                        // Handle error
                        wal(
                            'Oops...',
                            'Algo salió mal intente de nuevo o refresque la página!',
                            'error'
                        )
                    }

                });

            });
        });

    </script>
    @stop

