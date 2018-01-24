$(document).ready(function () {

    $("#modalCargando").modal();

    var mydata = [
    ];

    var  token, url, data;
    token = $('input[name=_token]').val();

    $.ajax({
        url: subfolder +'/inspecciones',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        datatype: 'JSON',
        data: {'idCentro':$('#idCentro').val()},
        success: function (resp) {
            $('#modalCargando').modal('hide');
            if(resp.inspecciones != null){
                $('#noregister').hide();
                $.each(resp.inspecciones, function (key, value){
                    item = {}
                    item ["id"] = value.id_inspeccion;
                    item ["numero_inspeccion"] = value.numero_inspeccion;
                    item ["fecha"] = value.fecha;
                    item ["encargado"] = value.encargado;
                    item ["puntaje"] = value.puntaje;
                    item ["documento"] = value.documento;
                    item ["estado"] = value.estado;
                    mydata.push(item);
                });

                // Configuration for jqGrid Example 1
                $("#table_list_6").jqGrid({
                    data: mydata,
                    datatype: "local",
                    height: 330,
                    autowidth: true,
                    shrinkToFit: true,
                    rowNum: 14,
                    rowList: [10, 20, 30],
                    colNames: ['No. de evaluación', 'Fecha de la evaluación', 'Evaluado por', 'Puntaje', 'Documento', 'Estado','Opciones'],
                    colModel: [
                        {name: 'numero_inspeccion', index: 'numero_inspeccion', width: 140, sortable: true, align: 'center'},
                        {name: 'fecha', index: 'fecha', width: 170, sorttype: "date", formatter: "date", formatoptions : {srcformat : 'Y-m-d H:i',newformat : 'd/m/Y'},align: 'center'},
                        {name: 'encargado', index: 'encargado', width: 150, align: 'center'},
                        {name: 'puntaje', index: 'puntaje', width: 150, align: 'center',formatter:'currency', formatoptions:{thousandsSeparator: ",", decimalPlaces: 0, suffix: "%"}},
                        {name: 'documento', index: 'documento', width: 200, align: 'center'},
                        {name: 'estado', index: 'estado', width: 90, align: 'center'},   
                        {name: 'name', index: 'name', width: 90, align: 'center'}                    
                    ],
                    pager: "#pager_list_6",
                    viewrecords: true,
                    gridComplete: function() {
                        var grid = $("#table_list_6");
                        var ids = grid.jqGrid('getDataIDs');
                        for (var i = 0; i < ids.length; i++) {
                            var rowId = ids[i];
                            var checkOut = '<a data-toggle="modal" class="btn-primary btn-sm" style="color:white;" onclick="loadModal('+rowId+')">Opciones</a>';
                            grid.jqGrid('setRowData', rowId, { name: checkOut });
                            if(mydata[i]["estado"]=='revisada')
                                checkOut = '<span data-toggle="modal" class="btn-primary btn-sm" style="color:black; background-color:#70AD47;"> Revisada  </span>';
                            if(mydata[i]["estado"]=='revision')
                                checkOut = '<span data-toggle="modal" class="btn-primary btn-sm" style="color:black; background-color:#ED7D31;">En revisión</span>';
                            if(mydata[i]["estado"]=='anulada')
                                checkOut = '<span data-toggle="modal" class="btn-primary btn-sm" style="color:black; background-color:#AFABAB;">  Anulada  </span>';
                            grid.jqGrid('setRowData', rowId, { estado: checkOut });
                        }
                    },
                    hidegrid: false,
                    scrollOffset: 0
                });
            }
        }
    });

    //Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        $('#table_list_6').setGridWidth(width);
    });
});

function loadModal(rowId){
    var nameProject = jQuery('#table_list_6').jqGrid ('getCell', rowId, 'numero_inspeccion');
    $('#nameProject').html('');
    $('#nameProject').append(nameProject);
    $("#editInspeccionLinkModal").attr("href", subfolder +"#");
    $("#descargarInspeccionLinkModal").attr("href", subfolder +"/inspeccion_descargar?idInspeccion="+rowId);
    $("#cambiarRevisionInspeccionLinkModal").attr("href", subfolder +"/inspeccion_cambiar_estado?idCentro="+$('#idCentro').val()+"&newEstado=revision&idInspeccion="+rowId);
    $("#cambiarRevisadaInspeccionLinkModal").attr("href", subfolder +"/inspeccion_cambiar_estado?idCentro="+$('#idCentro').val()+"&newEstado=revisada&idInspeccion="+rowId);
    $("#cambiarAnuladaInspeccionLinkModal").attr("href", subfolder +"/inspeccion_cambiar_estado?idCentro="+$('#idCentro').val()+"&newEstado=anulada&idInspeccion="+rowId);
    $("#idInspeccion").val(rowId);
    $("#modal-form").modal();
}

$("#cambiarEstadoInspeccionLinkModal").click(function(){
    $("#first3").hide();
    $("#last3").show();
});
$('#modal-form').on('hidden.bs.modal', function () {
    $("#first3").show();
    $("#last3").hide();
})
