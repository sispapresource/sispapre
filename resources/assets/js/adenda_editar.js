$(document).ready(function () {
	$("#select-estados").val($("#estadoSel").val());
});
function save(idAdenda){
    var token = $('input[name=_token]').val();
    $("#button-save").prop('disabled', true);
    $.ajax({
        url: subfolder +'/adenda_editar_data',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data:  {idAdenda:$('#idAdenda').val(),estado:$('#select-estados').val(),fecha:$('#date_adenda').val(),nro_adenda:$('#nro_adenda').val(),descripcion:$('#descripcion_adenda').val()},
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
                    toastr.success('Adenda Actualizada con Exito');
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
                    toastr.error('Actualizacion de Adenda Fallida');
                }, 1300);
                setTimeout(function(){
                    window.location="/home";
                }, 2600);
            }
        }
    });
};