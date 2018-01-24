$(document).ready(function () {
    $("#modalCargando").modal();
    tableToGrid("#table_list_4", {
        height: 330,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 14,
        rowList: [10, 20, 30],
        colModel: [
            {name: 'Fecha', classes: 'strong', width: 100, align: 'center',sorttype: "date", formatter: "date", formatoptions : {srcformat : 'Y-m-d H:i',newformat : 'd/m/y H:i'}},
            {name: 'Usuario', align: 'center', width: 100},
        ],
        pager: "#pager_list_4",
        viewrecords: true,
        hidegrid: false,
        loadComplete: function () {
            $("#table_list_4").find(">tbody>tr.jqgrow:odd").addClass("myAltRowClassEven");
            $("#table_list_4").find(">tbody>tr.jqgrow:even").addClass("myAltRowClassOdd");
        },
        scrollOffset: 0
    });
    // Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        $('#table_list_4').setGridWidth(width);
    });

    $("#table_list_4").css('visibility', 'visible');
    $('#table_list_4').trigger('reloadGrid');
    $('#modalCargando').modal('hide');
});
