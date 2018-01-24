$(document).ready(function () {

    $("#modalCargando").modal();

    var mydata = [
    ];

    var  token,id_centro;
    token = $('input[name=_token]').val();
    id_centro = $('#idCentro').val();
    
    $.ajax({
        url: subfolder +'/cuentasu',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        datatype: 'JSON',
        data: {'idCentro':id_centro} ,
        success: function (resp) {

            $.each(resp.cuentas, function (key, value) {
                item = {}
                item ["id"] = value.id_cuenta;
                item ["cuenta"] = value.nombre_cuenta;
                item ["name"] = value.presupuesto;
                item ["porcentaje"] = value.porcentaje_avance;
                item ["teorico"] = value.teorico;
                item ["update"] = value.ultima_actualizacion;
                mydata.push(item);
            });

            $("#table_list_3").jqGrid({
                data: mydata,
                datatype: "local",
                height: 330,
                myType: 'GET',
                autowidth: true,
                shrinkToFit: true,
                rowNum: 14,
                rowList: [10, 20, 30],
                colNames: ['Código', 'Cuenta', 'Presupuesto', '% de Avance Físico', '% de Avance Teórico', 'Última actualización',''],
                colModel: [
                    {name: 'id', index: 'id', width: 80, align: 'center', classes: 'strong',sortable: true},
                    {name: 'cuenta', index: 'cuenta', width: 140, align: 'center'},
                    {name: 'name', index: 'name', width: 100, align: 'center', formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}},
                    {name: 'porcentaje', index: 'porcentaje', width: 120, align: 'center',editable: resp.fisico, formatter:'currency', formatoptions:{thousandsSeparator: ",", decimalPlaces: 0, suffix: "%"},
                        editoptions:{   
                            dataInit: function(element) {
                                $(element).keypress(function(e){
                                    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
                                        return false;
                                });
                            }
                        }
                    },
                    {name: 'teorico', index: 'teorico', width: 130, align: 'center',editable: resp.teorico, formatter:'currency', formatoptions:{thousandsSeparator: ",", decimalPlaces: 0, suffix: "%"},
                        editoptions:{   
                            dataInit: function(element) {
                                $(element).keypress(function(e){
                                    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
                                        return false;
                                });
                            }
                        }
                    },
                    {name: 'update', index: 'update', width: 130, sorttype: "date", formatter: "date", formatoptions : {srcformat : 'Y-m-d H:i',newformat : 'd-m-Y H:i'},align: 'center'},
                    {name: 'edit', index: 'edit', width: 70, align: 'center'}
                ],
                pager: "#pager_list_3",
                gridComplete: function(){ 
                    var ids = $("#table_list_3").jqGrid('getDataIDs'); 
                    for(var i=0;i < ids.length;i++){
                        var cl = ids[i]; 
                        be = '<a class="btn-primary btn-sm" style="color:white;" type="button" onclick=editbutton("'+cl+'","'+id_centro+'")>Editar</a>';
                        $("#table_list_3").jqGrid('setRowData',ids[i],{edit:be}); 
                    } 
                },
                editurl: subfolder +"/dataupdate",
                hidegrid: false,
                viewrecords: true,
                scrollOffset: 0
            });
            $('#modalCargando').modal('hide');
        }
    });

    // Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        $('#table_list_3').setGridWidth(width);
    });

});

function reload(id_centro,show){

    $("#modalCargando").modal();

    var mydata = [
    ];

    var token = $('input[name=_token]').val();

    inputCodigo = $('#inputCodigo').val();
    inputCuenta = $('#inputCuenta').val();

    $.ajax({
        url: subfolder +'/cuentasu',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data: {'codigoFilter':inputCodigo,'cuentaFilter':inputCuenta,'idCentro':id_centro} ,
        datatype: 'JSON',
        success: function (resp) {

            $('#modalCargando').modal('hide');
            if(resp.cuentas != null){
                $.each(resp.cuentas, function (key, value) {

                    item = {}

                    item ["id"] = value.id_cuenta;
                    item ["cuenta"] = value.nombre_cuenta;
                    item ["name"] = value.presupuesto;
                    item ["porcentaje"] = value.porcentaje_avance;
                    item ["teorico"] = value.teorico;
                    item ["update"] = value.ultima_actualizacion;
                    mydata.push(item);

                });
            }

            if (mydata === undefined || mydata.length == 0) {
                if(!show){
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
                }

                jQuery('#table_list_3').jqGrid('clearGridData').trigger('reloadGrid');
            }else{      
                setTimeout(function() {
                    toastr.options = {
                        closeButton: true,
                        progressBar: false,
                        positionClass: "toast-top-center",
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    if(!show){
                        if(mydata.length==1)
                            toastr.success(resp.cuentas.length +' Registro encontrado');
                        if(mydata.length>1)
                            toastr.success(resp.cuentas.length +' Registros encontrados');
                    }
                    if(show)
                        toastr.success('Actualizacion Correcta');   
                }, 1300);
                jQuery('#table_list_3').jqGrid('clearGridData').jqGrid('setGridParam', {data: mydata}).trigger('reloadGrid');
            }
        }
    });
}

function editbutton(cl,id_centro){
    $('#table_list_3').jqGrid('editRow', cl,{ 
        keys : true, 
        extraparam : {idCentro:id_centro},
        mtype: "GET",
        successfunc: function(response,postdata) {
            if(response.responseText=='{"response":"success"}')
                reload(id_centro,true);      
            else{
                setTimeout(function(){
                    toastr.options = {
                        closeButton: true,
                        progressBar: false,
                        positionClass: "toast-top-center",
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    toastr.error('Actualizacion Fallida');
                }, 1300);
            }    
        }
    });
}

