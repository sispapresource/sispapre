$(document).ready(function () {

    $("#modalCargando").modal();

    var mydata = [
    ];

    var  token, url, data;
    token = $('input[name=_token]').val();

    $.ajax({
        url: subfolder +'/ajustes',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        datatype: 'JSON',
        data: {'idAdenda':$('#idAdenda').val()},
        success: function (resp) {
            $('#modalCargando').modal('hide');
            if(resp.ajustes != null){
                $('#noregister').hide();
                $.each(resp.ajustes, function (key, value) {
                    item = {}
                    item ["id"] = value.id_ajuste;
                    item ["nro_documento"] = value.nro_documento;
                    item ["nro_ajuste"] = value.nro_ajuste;
                    item ["fecha"] = value.fecha;
                    item ["descripcion"] = value.descripcion;
                    item ["cant_items"] = value.cant_items;
                    item ["costo"] = value.costo;
                    item ["utilidad"] = value.utilidad;
                    item ["admin"] = value.admin;
                    item ["subtotal"] = value.subtotal;
                    item ["itbms"] = value.itbms;
                    item ["total"] = value.total;
                    mydata.push(item);
                });
                // Configuration for jqGrid Example 1
                $("#table_list_6").jqGrid({
                    data: mydata,
                    datatype: "local",
                    height: 330,
                    autowidth: true,
                    shrinkToFit: false,
                    rowNum: 14,
                    rowList: [10, 20, 30],
                    colNames: ['No. de documento', 'No. de ajuste', 'Fecha de ajuste', 'Cant. Items', 'Descripcion', 'Costo','Utilidad','Admin.','Subtotal','ITBMS','Total', 'Opciones'],
                    colModel: [
                        {name: 'nro_documento', index: 'nro_documento', width: 160, sortable: true, align: 'center'},
                        {name: 'nro_ajuste', index: 'nro_ajuste', width: 140, align: "right", align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:subfolder +'/ajuste_detail', idName:'idAjuste'}},
                        {name: 'fecha', index: 'fecha', width: 140, sorttype: "date", formatter: "date", formatoptions : {srcformat : 'Y-m-d H:i',newformat : 'd/m/Y'},align: 'center'},
                        {name: 'cant_items', index: 'cant_items', width: 100, sortable: true, align: 'center'},
                        {name: 'descripcion', index: 'descripcion', width: 180, sorttype: "date",align: 'center'},
                        {name: 'costo', index: 'costo', width: 100, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                        {name: 'utilidad', index: 'utilidad', width: 100, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                        {name: 'admin', index: 'admin', width: 100, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                        {name: 'subtotal', index: 'subtotal', width: 100, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                        {name: 'itbms', index: 'itbms', width: 100, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                        {name: 'total', index: 'total', width: 100, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
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
    var nameProject = jQuery('#table_list_6').jqGrid ('getCell', rowId, 'note');
    $('#nameProject').html('');
    $('#nameProject').append(nameProject);
    $("#editAjusteLinkModal").attr("href", subfolder +"/ajuste_editar?idAjuste="+rowId);
    $("#viewAdendaLinkModal").attr("href", subfolder +"/ajuste_detail?idAjuste="+rowId);
    $("#createItemLinkModal").attr("href", subfolder +"/item_crear?idAjuste="+rowId);
    $("#exportAjusteExcel").attr("href", subfolder +"/export_ajuste/excel/"+rowId);
    $("#exportAjustePdf").attr("href", subfolder +"/export_ajuste/pdf/"+rowId);
    $("#idAdenda").val(rowId);
    $("#modal-form").modal();
}