$(document).ready(function () {

    $("#modalCargando").modal();

    var mydata = [
    ];

    var  token, url, data;
    token = $('input[name=_token]').val();

    $.ajax({
        url: subfolder +'/hallazgos',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        datatype: 'JSON',
        data: {'idCentro':$('#idCentro').val()},
        success: function (resp) {
            $('#modalCargando').modal('hide');
            if(resp.hallazgos != null){
                $('#noregister').hide();
                $.each(resp.hallazgos, function (key, value){
                    item = {}
                    item ["id"] = value.id_hallazgo;
                    item ["numero_hallazgo"] = value.numero_hallazgo;
                    item ["fecha"] = value.fecha;
                    item ["encargado"] = value.encargado;
                    item ["referencia"] = value.referencia;
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
                    colNames: ['No. de hallazgo', 'Fecha del hallazgo', 'Inspeccionado por', 'Referencia', 'Documento', 'Estado','Opciones'],
                    colModel: [
                        {name: 'numero_hallazgo', index: 'numero_hallazgo', width: 140, sortable: true, align: 'center'},
                        {name: 'fecha', index: 'fecha', width: 170, sorttype: "date", formatter: "date", formatoptions : {srcformat : 'Y-m-d H:i',newformat : 'd/m/Y'},align: 'center'},
                        {name: 'encargado', index: 'encargado', width: 150, align: 'center'},
                        {name: 'referencia', index: 'referencia', width: 150, align: 'center'},
                        {name: 'documento', index: 'documento', width: 200, align: 'center'},
                        {name: 'estado', index: 'estado', width: 100, align: 'center'},   
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
                            if(mydata[i]["estado"]=='subsanado')
                                checkOut = '<span data-toggle="modal" class="btn-primary btn-sm" style="color:white; background-color:#70AD47;"> Subsanado </span>';
                            if(mydata[i]["estado"]=='subsanar')
                                checkOut = '<span data-toggle="modal" class="btn-primary btn-sm" style="color:white; background-color:red;">Por subsanar</span>';
                            if(mydata[i]["estado"]=='anulado')
                                checkOut = '<span data-toggle="modal" class="btn-primary btn-sm" style="color:white; background-color:#AFABAB;">    Anulado    </span>';
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
    var nameProject = jQuery('#table_list_6').jqGrid ('getCell', rowId, 'numero_hallazgo');
    $('#nameProject').html('');
    $('#nameProject').append(nameProject);
    $("#editHallazgoLinkModal").attr("href", subfolder +"#");
    $("#descargarHallazgoLinkModal").attr("href", subfolder +"/hallazgo_descargar?idHallazgo="+rowId);
    $("#cambiarSubsanarHallazgoLinkModal").attr("href", subfolder +"/hallazgo_cambiar_estado?idCentro="+$('#idCentro').val()+"&newEstado=subsanar&idHallazgo="+rowId);
    $("#cambiarSubsanadoHallazgoLinkModal").attr("href", subfolder +"/hallazgo_cambiar_estado?idCentro="+$('#idCentro').val()+"&newEstado=subsanado&idHallazgo="+rowId);
    $("#cambiarAnuladoHallazgoLinkModal").attr("href", subfolder +"/hallazgo_cambiar_estado?idCentro="+$('#idCentro').val()+"&newEstado=anulado&idHallazgo="+rowId);
    $("#idHallazgo").val(rowId);
    $("#modal-form").modal();
}

$("#cambiarEstadoHallazgoLinkModal").click(function(){
    $("#first3").hide();
    $("#last3").show();
});
$('#modal-form').on('hidden.bs.modal', function () {
    $("#first3").show();
    $("#last3").hide();
})