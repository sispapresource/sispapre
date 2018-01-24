var subfolder='.';

$('#data_1 .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});

function save_ajuste(idAdenda){
    var token = $('input[name=_token]').val();
    $("#button-save-ajuste").prop('disabled', true);
    $.ajax({
        url: subfolder +'/ajuste_guardar',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data:  {idAdenda:idAdenda,fecha:$('#date_ajuste').val(),nro_ajuste:$('#nro_ajuste').val(),descripcion:$('#descripcion_ajuste').val(),utilidad:$('#utilidad').val(),administracion:$('#administracion').val(),itbms:$('#itbms').val()},
        success:  function(response){
            if(response['response']=="success"){
                setTimeout(function(){
                    toastr.options = {
                        closeButton: true,
                        progressBar: false,
                        positionClass: "toast-top-center",
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    toastr.success('Ajuste Creado con Exito');
                }, 1300);
                setTimeout(function(){
                    window.location= subfolder+"/adenda_detail?idAdenda="+idAdenda;
                }, 2600);    
            }
            else{
                setTimeout(function(){
                    toastr.options = {
                        closeButton: true,
                        progressBar: false,
                        positionClass: "toast-top-center",
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    toastr.error('Creacion de Ajuste Fallida');
                }, 1300);
                setTimeout(function(){
                    window.location= subfolder+"/adenda_detail?idAdenda="+idAdenda;
                }, 2600);
            }
        }
    });
};

function edit_ajuste(idAjuste){
    var token = $('input[name=_token]').val();
    $("#button-save").prop('disabled', true);
    $.ajax({
        url: subfolder +'/ajuste_editar_data',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data:  {idAjuste:$('#idAjuste').val(),utilidad:$('#utilidad').val(),administracion:$('#administracion').val(),itbms:$('#itbms').val(),fecha:$('#date_ajuste').val(),nro_ajuste:$('#nro_ajuste').val(),descripcion:$('#descripcion_ajuste').val()},
        success:  function(response){
            if(response['response']=="success"){
                setTimeout(function(){
                    toastr.options = {
                        closeButton: true,
                        progressBar: false,
                        positionClass: "toast-top-center",
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    toastr.success('Ajuste Actualizado con Exito');
                }, 1300);
                setTimeout(function(){
                    window.location="/home";
                }, 2600);    
            }
            else{
                setTimeout(function(){
                    toastr.options = {
                        closeButton: true,
                        progressBar: false,
                        positionClass: "toast-top-center",
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    toastr.error('Actualizacion de Ajuste Fallida');
                }, 1300);
                setTimeout(function(){
                    window.location="/home";
                }, 2600);
            }
        }
    });
};