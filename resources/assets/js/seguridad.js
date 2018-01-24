//$(document).ready(function () {
//    $("#modalCargando").modal();
//    var mydata = [];
//    var  token, url, data;
//    token = $('input[name=_token]').val();
//    $.ajax({
//        url: subfolder +'/seguridad',
//        headers: {'X-CSRF-TOKEN': token},
//        type: 'GET',
//        datatype: 'JSON',
//        success: function (resp) {
//            $('#modalCargando').modal('hide');
//            if(resp.inspecciones != null){
//                $('#noregister').hide();
//                $.each(resp.inspecciones, function (key, value) {
//                    item = {}
//                    item ["id"] = value.id_centro;
//                    item ["nombre_centro"] = value.nombre_centro;
//                    item ["encargado"] = value.encargado;
//                    item ["puntaje"] = value.puntaje;
//                    item ["fecha"] = value.fecha;
//                    item ["hallazgos"] = value.hallazgos;
//                    mydata.push(item);
//                });
//                // Configuration for jqGrid Example 1
//                $("#table_list_6").jqGrid({
//                    data: mydata,
//                    datatype: "local",
//                    height: 330,
//                    autowidth: true,
//                    shrinkToFit: true,
//                    rowNum: 14,
//                    rowList: [10, 20, 30],
//                    colNames: ['Nombre de proyecto', 'Encargado', 'Puntaje de ultima inspección', 'Fecha de ultima inspección', 'Hallazgos pendientes', 'Opciones'],
//                    colModel: [
//                        {name: 'nombre_centro', index: 'nombre_centro', width: 340, sortable: true, align: 'left'},
//                        {name: 'encargado', index: 'encargado', width: 150, align: 'center'},
//                        {name: 'puntaje', index: 'puntaje', width: 210, align: 'center',formatter:'currency', formatoptions:{thousandsSeparator: ",", decimalPlaces: 0, suffix: "%"}},
//                        {name: 'fecha', index: 'fecha', width: 200, sorttype: "date", formatter: "date", formatoptions : {srcformat : 'Y-m-d H:i',newformat : 'd/m/Y'},align: 'center'},
//                        {name: 'hallazgos', index: 'hallazgos', width: 160, align: 'center',formatter:'showlink', formatoptions:{baseLinkUrl:subfolder +'/home_hallazgos', idName:'idCentro'}},
//                        {name: 'name', index: 'name', width: 90, align: 'center'}                    
//                    ],
//                    pager: "#pager_list_6",
//                    viewrecords: true,
//                    gridComplete: function() {
//                        var grid = $("#table_list_6");
//                        var ids = grid.jqGrid('getDataIDs');
//                        for (var i = 0; i < ids.length; i++){
//                            var rowId = ids[i];
//                            var checkOut = '<a data-toggle="modal" class="btn-primary btn-sm" style="color:white;" onclick="loadModal('+rowId+')">Opciones</a>';
//                            grid.jqGrid('setRowData', rowId, { name: checkOut });
//                        }
//                    },
//                    hidegrid: false,
//                    scrollOffset: 0
//                });
//            }
//        }
//    });
//    //Add responsive to jqGrid
//    $(window).bind('resize', function () {
//        var width = $('.jqGrid_wrapper').width();
//        $('#table_list_6').setGridWidth(width);
//    });
//});
//
//function reload(action){
//    if(action == 'clear'){
//        $('#textFilter').val('');
//    }
//    $("#modalCargando").modal();
//    var mydata = [];
//    var  token, url, data;
//    token = $('input[name=_token]').val();
//    filterText = $('#textFilter').val();
//    $.ajax({
//        url: subfolder +'/seguridad',
//        headers: {'X-CSRF-TOKEN': token},
//        type: 'GET',
//        data: {'textFilter':filterText},
//        datatype: 'JSON',
//        success: function (resp) {
//            $('#modalCargando').modal('hide');
//            if(resp.inspecciones != null){
//                $('#noregister').hide();
//                $.each(resp.inspecciones, function (key, value) {
//                    item = {}
//                    item ["id"] = value.id_centro;
//                    item ["nombre_centro"] = value.nombre_centro;
//                    item ["encargado"] = value.encargado;
//                    item ["puntaje"] = value.puntaje;
//                    item ["fecha"] = value.fecha;
//                    item ["hallazgos"] = value.hallazgos;
//                    mydata.push(item);
//                });
//                // Configuration for jqGrid Example 1
//                if (mydata === undefined || mydata.length == 0) {
//                    setTimeout(function() {
//                        toastr.options = {
//                            closeButton: true,
//                            progressBar: false,
//                            positionClass: "toast-top-center",
//                            showMethod: 'slideDown',
//                            timeOut: 4000
//                        };
//                        toastr.success('No se encontraron registros');
//                    }, 1300);
//                    jQuery('#table_list_6').jqGrid('clearGridData').trigger('reloadGrid');
//                }else{
//                    setTimeout(function() {
//                        toastr.options = {
//                            closeButton: true,
//                            progressBar: false,
//                            positionClass: "toast-top-center",
//                            showMethod: 'slideDown',
//                            timeOut: 4000
//                        };
//                        if(mydata.length==1)
//                            toastr.success(resp.centros.length +' Registro encontrado');
//                        if(mydata.length>1)
//                            toastr.success(resp.centros.length +' Registros encontrados');
//                    }, 1300);
//                    $("#table_list_6")[0].addJSONData({
//                        total: 1,
//                        page: 1,
//                        records: mydata.length,
//                        rows: mydata
//                    });
//                }
//            }
//        }
//    });
//}
//
//function loadModal(rowId){
//    var nameProject = jQuery('#table_list_6').jqGrid ('getCell', rowId, 'nombre_centro');
//    $('#nameProject').html('');
//    $('#nameProject').append(nameProject);
//    $("#verInspeccionesLinkModal").attr("href", subfolder +"/home_inspecciones?idCentro="+rowId);
//    $("#verHallazgosLinkModal").attr("href", subfolder +"/home_hallazgos?idCentro="+rowId);
//    $("#crearInspeccionLinkModal").attr("href", "#");
//    $("#crearHallazgoLinkModal").attr("href", subfolder +"/hallazgo_crear?centro="+rowId);
//    $("#modal-form").modal();
//}
