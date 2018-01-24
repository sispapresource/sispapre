/* $(document).ready(function () {
    $("#selectcentro").val($("#idCentro").val());
    $("#inputCategoria").val($("#catFilter").val().split(','));
    $("#dateFilterDesde").val($("#fechaDesde").val());
    $("#dateFilterHasta").val($("#fechaHasta").val());
    $("#montoFilterDesde").val($("#montoDesde").val());
    $("#montoFilterHasta").val($("#montoHasta").val());
    $("#proveedorFilter").val($("#proveedor").val());
    $("#modalCargando").modal();

    //    tableToGrid("#table_gastado", {
    //        height: 600,
    //        autowidth: false,
    //        shrinkToFit: true,
    //        rowNum: 50,
    //        rowList: [50, 100, 500],
    //        colModel: [
    //            {name: 'Codigo', align: 'center', width: 50},
    //            {name: 'Fecha', classes: 'strong', width: 50, align: 'center',sorttype: "date", formatter: "date", formatoptions : {srcformat : 'Y-m-d H:i',newformat : 'd-m-Y H:i'}},
    //            {name: 'Proveedor', align: 'center', width: 50},
    //            {name: 'No._de_documento', align: 'center', width: 50},
    //            {name: 'Descripción', align: 'center', width: 50},
    //            {name: 'Monto', align: 'center', width: 50, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float"}
    //        ],
    //        pager: "#pager_list_4",
    //        viewrecords: true,
    //        hidegrid: false,
    //        scrollOffset: 0,
    //        loadComplete: function () {
    //            $("#table_gastado").find(">tbody>tr.jqgrow:odd").addClass("myAltRowClassEven");
    //            $("#table_gastado").find(">tbody>tr.jqgrow:even").addClass("myAltRowClassOdd");
    //        }
    //    });
    //    // Add responsive to jqGrid
    //    $(window).bind('resize', function () {
    //        var width = $('.jqGrid_wrapper').width();
    //        $('#table_gastado').setGridWidth(width);
    //    });

    //    $("#table_gastado").css('visibility', 'visible');
    //    $('#table_gastado').trigger('reloadGrid');

    $("#selectcuenta").chosen({width: "100%"});
    var selectedValues = $("#idCuenta").val().split(",");
    $("#selectcuenta").val(selectedValues);
    $("#selectcuenta").trigger("chosen:updated");

    $('#modalCargando').modal('hide');
});

$(function () {
    $('#inputCategoria').multipleSelect({selectAll: false,
                                         countSelected: "# elementos seleccionados",
                                         minimumCountSelected: 6,
                                         placeholder: 'Seleccione la(s) categoria(s)'});
});

$("#divGastado").on("keypress", ".amountNew", function(e){
    if (e.which != 45 && e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) // -, retroceso, nulo, punto, numeros
        return false; 
});

function reload_gastado(action){
    if(action == 'clear'){
        $("#inputCategoria").val('');
        $("#dateFilterDesde").val('');
        $("#dateFilterHasta").val('');
        $("#montoFilterDesde").val('');
        $("#montoFilterHasta").val('');
        $("#proveedorFilter").val('');
        inputCuenta="";
        inputCategoria="";
    }

    inputCuenta = $('#selectcuenta').val();
    inputCategoria = $('#inputCategoria').val();
    if(inputCategoria!=null)
        inputCategoria+=',';
    $.ajax({
        type: "GET",
        url: subfolder + "/gastado",
        traditional:true,
        data: {
            'proveedorFilter':$("#proveedorFilter").val(),
            'idCentro':$("#selectcentro").val(),
            'idCuenta':inputCuenta,
            'categoriaFilter':inputCategoria, 
            'dateFilterDesde': $('#dateFilterDesde').val(), 
            'dateFilterHasta': $('#dateFilterHasta').val(), 
            'montoFilterDesde': $('#montoFilterDesde').val(), 
            'montoFilterHasta': $('#montoFilterHasta').val()
        },
        datatype: 'JSON',
        cache: false,    
        success: function(html){                  
            $("#app-layout").html(html);
        }
    });
}
//function reload_comprometido(action){
//    if(action == 'clear'){
//        $("#inputCategoria").val('');
//        $("#dateFilterDesde").val('');
//        $("#dateFilterHasta").val('');
//        $("#montoFilterDesde").val('');
//        $("#montoFilterHasta").val('');
//        $("#proveedorFilter").val('');
//    }
//
//    inputCuenta = $('#selectcuenta').val();
//    inputCategoria = $('#inputCategoria').val();
//    if(inputCategoria!=null)
//        inputCategoria+=',';
//    $.ajax({
//        type: "GET",
//        url: subfolder + "/comprometido",
//        data: {
//            'proveedorFilter':$("#proveedorFilter").val(),
//            'idCentro':$("#selectcentro").val(),
//            'idCuenta':inputCuenta,
//            'categoriaFilter':inputCategoria, 
//            'dateFilterDesde': $('#dateFilterDesde').val(), 
//            'dateFilterHasta': $('#dateFilterHasta').val(), 
//            'montoFilterDesde': $('#montoFilterDesde').val(), 
//            'montoFilterHasta': $('#montoFilterHasta').val()
//        },
//        datatype: 'JSON',
//        cache: false,    
//        success: function(html){                  
//            $("#app-layout").html(html);
//        }
//    });
//} */