$(document).ready(function () {
    //	if($( "#tiposelect" ).val()=='ADMINISTRACION')
    //		$("#tiposelect").append('<option value="PRECIO FIJO">PRECIO FIJO</option>');
    //	else
    //		$("#tiposelect").append('<option value="ADMINISTRACION">ADMINISTRACION</option>');
});

function save(idCentro){
    var token = $('input[name=_token]').val();
    $("#button-save").prop('disabled', true);
    $.ajax({
        url: subfolder +'/update_details_data',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data:  {
            idCentro:idCentro,
            contratante:$('#contratante').val(),
            tel_contratante:$('#tel_contratante').val(),
            tipo:$('#tiposelect').val(),
            nombre_proyecto:$('#nombre_proyecto').val()
        },
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
                    toastr.success('Proyecto Actualizado con Exito');
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
                    toastr.error('Actualizacion de Proyecto Fallida');
                }, 1300);
                setTimeout(function(){
                    window.location="/home";
                }, 2600);
            }
        }
    });
};