function save(t){var o=$("input[name=_token]").val();$("#button-save").prop("disabled",!0),$.ajax({url:subfolder+"/update_details_data",headers:{"X-CSRF-TOKEN":o},type:"GET",data:{idCentro:t,contratante:$("#contratante").val(),tel_contratante:$("#tel_contratante").val(),tipo:$("#tiposelect").val(),nombre_proyecto:$("#nombre_proyecto").val()},success:function(t){"success"==t.response?(setTimeout(function(){toastr.options={closeButton:!0,progressBar:!1,positionClass:"toast-top-center",showMethod:"slideDown",timeOut:4e3},toastr.success("Proyecto Actualizado con Exito")},1300),setTimeout(function(){window.location="/home"},2600)):(setTimeout(function(){toastr.options={closeButton:!0,progressBar:!1,positionClass:"toast-top-center",showMethod:"slideDown",timeOut:4e3},toastr.error("Actualizacion de Proyecto Fallida")},1300),setTimeout(function(){window.location="/home"},2600))}})}$(document).ready(function(){});