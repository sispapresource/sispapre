$(document).ready(function () {
    // Examle data for jqGrid
    var mydata = [
        {id: "1", note: "<a href='#'>6-ODTprueba.pdf</a>", tax: "24/11/2016 12:09pm",  update: "Usuario de Prueba"}         
    ];

    // Configuration for jqGrid Example 1
    $("#table_list_5").jqGrid({
        data: mydata,
        datatype: "local",
        height: 30,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 14,
        rowList: [10, 20, 30],
        colNames: ['Nombre del archivo', 'Fecha y hora de subida', 'Usuario creador'],
        colModel: [
            {name: 'note', index: 'note', width: 50, sortable: true, align: 'center'},
            {name: 'tax', index: 'tax', width: 80, align: 'center'},
            {name: 'update', index: 'update', width: 150, align: 'center'}                  
        ],
        pager: "#pager_list_5",
        viewrecords: true,
        hidegrid: false,
        scrollOffset: 0
    });

    //Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        $('#table_list_5').setGridWidth(width);
    });

});