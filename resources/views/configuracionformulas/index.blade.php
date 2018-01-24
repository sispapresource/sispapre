@extends('layouts.app')

@section('content')


<div class="wrapper wrapper-content">
    <div class="container container-lg" id="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <i class="fa fa-calculator"></i> Configuracion</h5>
                    </div>
                    <!-- Main content -->

                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            @foreach($configs as $config)
                                <div class="row">
                                    <div class="col-md-8">
                                        {{$config->descripcion}}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::select($config->slug, $config->opciones->pluck('texto','id'), $config->id_valor, ['class'=>'form-control config', 'data-previous'=>$config->id_valor,'data-parent'=>$config->id]) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- /Main content -->


                </div>
            </div>


        </div>
    </div>
</div>


@endsection('content')

@section('page-script')
<script>
$(document).ready(function() {
    $('.config').on('change',function(){
        var value = $(this).val();
        var previous = $(this).data('previous');
        var parent = $(this).data('parent');
        var text = $(this.id+" option:selected").text();
        var me = this;
        swal({
            title: "Cambio en configuración",
            text: "Está seguro que desea cambiar el valor a "+text,
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function (isConfirm) {
            if(isConfirm) {
                $(me).data('previous', $(me).val());
                $.ajax({
                    url: "{{URL::to('configuracion/formula')}}/"+parent,
                    type: "PUT",
                    data: {
                        _token: "{{csrf_token()}}",
                        padre: value
                    },
                    dataType: "html",
                    success: function (res) {
                        swal("Exito!", "El valor ha sido configurado!", "success");
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        swal("Ocurrio un error!", "Porfavor intente de nuevo", "error");
                    }
                });
            }else{
                $(me).val($(me).data('previous'));
            }
        });
    });
});
</script>
@endsection('page-script')