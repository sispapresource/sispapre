$(document).ready(function () {

	$("#modalCargando").modal();

	var token = $('input[name=_token]').val();
    $.ajax({
        url: subfolder +'/centros',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        datatype: 'JSON',
        success: function (resp) {
            $.each(resp.centros, function (key, value) {
                $("#select-proyectos").append('<option value="'+value.id_centro+'">'+value.nombre_centro+'</option>');
            });
            $("#select-proyectos").val($('#idCentro').val());
            $('#modalCargando').modal('hide');
        }
    });
});

$("#div-select-proyectos").on("change", ".select-proyectos", function(){
    $("#proyecto-title").html("al Presupuesto - "+$("#select-proyectos option:selected").text());
});

function save(){
    var token = $('input[name=_token]').val();
    $("#button-save").prop('disabled', true);
    $.ajax({
        url: subfolder +'/adenda_guardar',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data:  {estado:$('#select-estados').val(),idCentro:$('#select-proyectos').val(),fecha:$('#date_adenda').val(),nro_adenda:$('#nro_adenda').val(),descripcion:$('#descripcion_adenda').val()},
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
                    toastr.success('Adenda Creada con Exito');
                }, 1300);
                setTimeout(function(){
                    window.location= subfolder +"/home_adendas";
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
                    toastr.error('Creacion de Adenda Fallida');
                }, 1300);
                setTimeout(function(){
                    window.location= subfolder + "/home_adendas";
                }, 2600);
            }
        }
    });
};