/*  var t_costo = t_utilidad = t_admin = t_subtotal = t_itbms = t_total = 0;

$(document).ready(function () {

    $("#modalCargando").modal();

    var mydata = [];

    var  token, url, data;
    token = $('input[name=_token]').val();

    $.ajax({
        url: subfolder +'/adendas',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        datatype: 'JSON',
        success: function (resp) {

            $('#modalCargando').modal('hide');
            if(resp.adendas != null){
                $('#noregister').hide();
                $.each(resp.adendas, function (key, value) {
                    item = {}
                    item ["id"] = value.id_adenda;
                    item ["note"] = value.proyecto;
                    item ["tax"] = value.fecha_documento;
                    item ["nro_adenda"] = value.nro_adenda;
                    item ["descripcion"] = value.descripcion;
                    item ["monto_adenda"] = value.monto;
                    item ["utilidad"] = value.utilidad;
                    item ["admin"] = value.admin;
                    item ["subtotal"] = value.subtotal;
                    item ["itbms"] = value.itbms;
                    item ["total"] = value.total;
                    item ["estado"] = value.estado;
                    if(value.monto)
                        t_costo += parseFloat(value.monto);
                    if(value.utilidad)
                        t_utilidad += parseFloat(value.utilidad);
                    if(value.admin)
                        t_admin += parseFloat(value.admin);
                    if(value.subtotal)
                        t_subtotal += parseFloat(value.subtotal);
                    if(value.itbms)
                        t_itbms += parseFloat(value.itbms);
                    if(value.total)
                        t_total += parseFloat(value.total);
                    mydata.push(item);
                });

                // Configuration for jqGrid Example 1
                $("#table_list_6").jqGrid({
                    data: mydata,
                    datatype: "local",
                    height: 660,
                    autowidth: true,
                    shrinkToFit: true,
                    rowNum: 20,
                    rowList: [20, 40, 60],
                    colNames: ['Proyecto', 'No. de documento', 'Fecha de doc.', 'Descripcion', 'Costo','Utilidad','Admin.','Subtotal','ITBMS','Total','Estado', 'Opciones'],
                    colModel: [
                        {name: 'note', index: 'note', width: 340, sortable: true, align: 'left'},
                        {name: 'nro_adenda', index: 'nro_adenda', width: 150, align: "right", align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:subfolder +'/adenda_detail', idName:'idAdenda'}},
                        {name: 'tax', index: 'tax', width: 120, sorttype: "date", formatter: "date", formatoptions : {srcformat : 'Y-m-d H:i',newformat : 'd/m/Y'},align: 'center'},
                        {name: 'descripcion', index: 'descripcion', width: 320, sorttype: "date",align: 'center'},
                        {name: 'monto_adenda', index: 'monto_adenda', width: 110, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                        {name: 'utilidad', index: 'utilidad', width: 110, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                        {name: 'admin', index: 'admin', width: 110, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                        {name: 'subtotal', index: 'subtotal', width: 110, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                        {name: 'itbms', index: 'itbms', width: 110, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                        {name: 'total', index: 'total', width: 110, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
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
                        }
                    },
                    hidegrid: false,
                    scrollOffset: 0
                });
                // add totales
                $("#table_list_6_monto_adenda").append('<h6>Total: '+toCurrency(t_costo)+'</h6>');
                $("#table_list_6_utilidad").append('<h6>Total: '+toCurrency(t_utilidad)+'</h6>');
                $("#table_list_6_admin").append('<h6>Total: '+toCurrency(t_admin)+'</h6>');
                $("#table_list_6_subtotal").append('<h6>Total: '+toCurrency(t_subtotal)+'</h6>');
                $("#table_list_6_itbms").append('<h6>Total: '+toCurrency(t_itbms)+'</h6>');
                $("#table_list_6_total").append('<h6>Total: '+toCurrency(t_total)+'</h6>');
            }
        }
    });

    //Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        $('#table_list_6').setGridWidth(width);
    });
    $("#UploadLinkModal").click(function(){
        $('#modal-form').modal('toggle');
        $('#modal-upload').modal();
    });
    
});

function reload(action){

    if(action == 'clear'){
        $('#textFilter').val('');
        $('#dateFilter').val('');
    }

    $("#modalCargando").modal();

    var mydata = [];
    var t_costo = t_utilidad = t_admin = t_subtotal = t_itbms = t_total = 0;

    var token = $('input[name=_token]').val();

    filterText = $('#textFilter').val();
    filterText1 = $('#select-estados').val();
    filterText2 = $('#dateFilter').val();
    filterText3 = $('#numeroFilter').val();

    $.ajax({
        url: subfolder +'/adendas',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data: {'textFilter':filterText,'estadoFilter':filterText1, 'dateFilter':filterText2,'numeroFilter':filterText3},
        datatype: 'JSON',
        success: function (resp) {

            $('#modalCargando').modal('hide');

            if(resp.adendas != null){
                $.each(resp.adendas, function (key, value) {
                    item = {}
                    item ["id"] = value.id_adenda;
                    item ["note"] = value.proyecto;
                    item ["tax"] = value.fecha_documento;
                    item ["nro_adenda"] = value.nro_adenda;
                    item ["descripcion"] = value.descripcion;
                    item ["monto_adenda"] = value.monto;
                    item ["utilidad"] = value.utilidad;
                    item ["admin"] = value.admin;
                    item ["subtotal"] = value.subtotal;
                    item ["itbms"] = value.itbms;
                    item ["total"] = value.total;
                    item ["estado"] = value.estado;
                    if(value.monto)
                        t_costo += parseFloat(value.monto);
                    if(value.utilidad)
                        t_utilidad += parseFloat(value.utilidad);
                    if(value.admin)
                        t_admin += parseFloat(value.admin);
                    if(value.subtotal)
                        t_subtotal += parseFloat(value.subtotal);
                    if(value.itbms)
                        t_itbms += parseFloat(value.itbms);
                    if(value.total)
                        t_total += parseFloat(value.total);
                    mydata.push(item);
                });
            }

            if (mydata === undefined || mydata.length == 0) {

                setTimeout(function() {
                    toastr.options = {
                        closeButton: true,
                        progressBar: false,
                        positionClass: "toast-top-center",
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    toastr.success('No se encontraron registros');
                }, 1300);

                //delete totales
                $("#table_list_6_monto_adenda h6").html('');
                $("#table_list_6_utilidad h6").html('');
                $("#table_list_6_admin h6").html('');
                $("#table_list_6_subtotal h6").html('');
                $("#table_list_6_itbms h6").html('');
                $("#table_list_6_total h6").html('');

                jQuery('#table_list_6').jqGrid('clearGridData').trigger('reloadGrid');
                
            }else{

                setTimeout(function() {
                    toastr.options = {
                        closeButton: true,
                        progressBar: false,
                        positionClass: "toast-top-center",
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    if(mydata.length==1)
                        toastr.success(resp.adendas.length +' Registro encontrado');
                    if(mydata.length>1)
                        toastr.success(resp.adendas.length +' Registros encontrados');
                }, 1300);

                // add totales
                $("#table_list_6_monto_adenda h6").html('Total: '+toCurrency(t_costo));
                $("#table_list_6_utilidad h6").html('Total: '+toCurrency(t_utilidad));
                $("#table_list_6_admin h6").html('Total: '+toCurrency(t_admin));
                $("#table_list_6_subtotal h6").html('Total: '+toCurrency(t_subtotal));
                $("#table_list_6_itbms h6").html('Total: '+toCurrency(t_itbms));
                $("#table_list_6_total h6").html('Total: '+toCurrency(t_total));

                jQuery('#table_list_6').jqGrid('clearGridData')
                    .jqGrid('setGridParam', {data: mydata})
                    .trigger('reloadGrid');
            }
        }
    });
}

function toCurrency(value){
    return "$"+value.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
}

function loadModal(rowId){
    var nameProject = jQuery('#table_list_6').jqGrid ('getCell', rowId, 'note');
    $('#nameProject').html('');
    $('#nameProject').append(nameProject);
    $("#viewAdendaLinkModal").attr("href", subfolder +"/adenda_detail?idAdenda="+rowId);
    $("#createAjusteLinkModal").attr("href", subfolder +"/ajuste_crear?idAdenda="+rowId);
    $("#editAdendaLinkModal").attr("href", subfolder +"/adenda_editar?idAdenda="+rowId);
    $("#exportAdendaExcel").attr("href", subfolder +"/export_adenda/excel/"+rowId);
    $("#exportAdendaPdf").attr("href", subfolder +"/export_adenda/pdf/"+rowId);
    $("#idAdenda").val(rowId);
    $("#modal-form").modal();
}

//function exportHomeAdendas(){
//    $("#modalCargando").modal();
//    var token = $('input[name=_token]').val();
//    $.ajax({
//        url: subfolder +'/export_home_adendas',
//        headers: {'X-CSRF-TOKEN': token},
//        type: 'POST',
//        data:  {nombreFilter:$('#textFilter').val(), estadoFilter:$('#select-estados').val()},
//        success:  function(response){
//            $('#modalCargando').modal('hide');
//            window.location.href = subfolder + response;
//        }
//    }); 
//} */