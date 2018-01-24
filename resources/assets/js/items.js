$(document).ready(function () {

    $("#modalCargando").modal();

    var mydata = [
    ];

    var  token, url, data;
    token = $('input[name=_token]').val();

    $.ajax({
        url: subfolder +'/items',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        datatype: 'JSON',
        data: {'idAjuste':$('#idAjuste').val()},
        success: function (resp) {
            $('#modalCargando').modal('hide');
            if(resp.fields != null){
                $('#noregister').hide();
                $.each(resp.fields, function (key, value) {
                    item = {}
                    item ["id"] = value.id_item;
                    item ["nro_documento"] = value.nro_documento;
                    item ["nro_ajuste"] = value.nro_ajuste;
                    item ["nro_item"] = value.nro_item;
                    item ["fecha"] = value.fecha;
                    item ["costo"] = value.costo;
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
                    colNames: ['No. de documento', 'No. de ajuste', 'No. Item', 'Fecha de ajuste', 'Costo','Opciones'],
                    colModel: [
                        {name: 'nro_documento', index: 'nro_documento', width: 160, sortable: true, align: 'center'},
                        {name: 'nro_ajuste', index: 'nro_ajuste', width: 140, align: "right", align: 'center'},
                        {name: 'nro_item', index: 'nro_item', width: 160, sortable: true, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:subfolder +'/item_detail', idName:'idItem'}},
                        {name: 'fecha', index: 'fecha', width: 140, sorttype: "date", formatter: "date", formatoptions : {srcformat : 'Y-m-d H:i',newformat : 'd/m/Y'},align: 'center'},
                        {name: 'costo', index: 'costo', width: 100, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
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
    $("#viewAdendaLinkModal").attr("href", subfolder +"/item_detail?idItem="+rowId);
    $("#exportAjusteExcel").attr("href", subfolder +"/export_ajuste/excel/"+ $('#idAjuste').val());
    $("#exportAjustePdf").attr("href", subfolder +"/export_ajuste/pdf/"+ $('#idAjuste').val());
    $("#modal-form").modal();
    $("#idItem").val(rowId);
}