function loadModal(a){var e=jQuery("#table_list_6").jqGrid("getCell",a,"numero_hallazgo");$("#nameProject").html(""),$("#nameProject").append(e),$("#editHallazgoLinkModal").attr("href",subfolder+"#"),$("#descargarHallazgoLinkModal").attr("href",subfolder+"/hallazgo_descargar?idHallazgo="+a),$("#cambiarSubsanarHallazgoLinkModal").attr("href",subfolder+"/hallazgo_cambiar_estado?idCentro="+$("#idCentro").val()+"&newEstado=subsanar&idHallazgo="+a),$("#cambiarSubsanadoHallazgoLinkModal").attr("href",subfolder+"/hallazgo_cambiar_estado?idCentro="+$("#idCentro").val()+"&newEstado=subsanado&idHallazgo="+a),$("#cambiarAnuladoHallazgoLinkModal").attr("href",subfolder+"/hallazgo_cambiar_estado?idCentro="+$("#idCentro").val()+"&newEstado=anulado&idHallazgo="+a),$("#idHallazgo").val(a),$("#modal-form").modal()}$(document).ready(function(){$("#modalCargando").modal();var a,e=[];a=$("input[name=_token]").val(),$.ajax({url:subfolder+"/hallazgos",headers:{"X-CSRF-TOKEN":a},type:"GET",datatype:"JSON",data:{idCentro:$("#idCentro").val()},success:function(a){$("#modalCargando").modal("hide"),null!=a.hallazgos&&($("#noregister").hide(),$.each(a.hallazgos,function(a,o){item={},item.id=o.id_hallazgo,item.numero_hallazgo=o.numero_hallazgo,item.fecha=o.fecha,item.encargado=o.encargado,item.referencia=o.referencia,item.documento=o.documento,item.estado=o.estado,e.push(item)}),$("#table_list_6").jqGrid({data:e,datatype:"local",height:330,autowidth:!0,shrinkToFit:!0,rowNum:14,rowList:[10,20,30],colNames:["No. de hallazgo","Fecha del hallazgo","Inspeccionado por","Referencia","Documento","Estado","Opciones"],colModel:[{name:"numero_hallazgo",index:"numero_hallazgo",width:140,sortable:!0,align:"center"},{name:"fecha",index:"fecha",width:170,sorttype:"date",formatter:"date",formatoptions:{srcformat:"Y-m-d H:i",newformat:"d/m/Y"},align:"center"},{name:"encargado",index:"encargado",width:150,align:"center"},{name:"referencia",index:"referencia",width:150,align:"center"},{name:"documento",index:"documento",width:200,align:"center"},{name:"estado",index:"estado",width:100,align:"center"},{name:"name",index:"name",width:90,align:"center"}],pager:"#pager_list_6",viewrecords:!0,gridComplete:function(){for(var a=$("#table_list_6"),o=a.jqGrid("getDataIDs"),t=0;t<o.length;t++){var l=o[t],d='<a data-toggle="modal" class="btn-primary btn-sm" style="color:white;" onclick="loadModal('+l+')">Opciones</a>';a.jqGrid("setRowData",l,{name:d}),"subsanado"==e[t].estado&&(d='<span data-toggle="modal" class="btn-primary btn-sm" style="color:white; background-color:#70AD47;"> Subsanado </span>'),"subsanar"==e[t].estado&&(d='<span data-toggle="modal" class="btn-primary btn-sm" style="color:white; background-color:red;">Por subsanar</span>'),"anulado"==e[t].estado&&(d='<span data-toggle="modal" class="btn-primary btn-sm" style="color:white; background-color:#AFABAB;">    Anulado    </span>'),a.jqGrid("setRowData",l,{estado:d})}},hidegrid:!1,scrollOffset:0}))}}),$(window).bind("resize",function(){var a=$(".jqGrid_wrapper").width();$("#table_list_6").setGridWidth(a)})}),$("#cambiarEstadoHallazgoLinkModal").click(function(){$("#first3").hide(),$("#last3").show()}),$("#modal-form").on("hidden.bs.modal",function(){$("#first3").show(),$("#last3").hide()});