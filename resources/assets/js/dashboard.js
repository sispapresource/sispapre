Array.prototype.removeValue = function(name, value){
    var array = $.map(this, function(v,i){
        return v[name] === value ? null : v;
    });
    this.length = 0; //clear original array
    this.push.apply(this, array); //push all elements except the one we want to delete
}

$(document).ready(function () {

    $("#modalCargando").modal();    
    $('#data_1 .input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true
    });
    var mydata = []; 
    var  token, url, data;
    token = $('input[name=_token]').val();

    var anticipopagos, estadop, inactivos, docp;

    $.ajax({
        url: subfolder + '/centros',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        datatype: 'JSON',
        success: function (resp) {
            anticipopagos =resp.anticipopagos;
            estadop =resp.estadop;
            docp =resp.docp;
            var presupuestoxgastar = parseInt(resp.presupuestoxgastar);
            $('#modalCargando').modal('hide');
            $.each(resp.centros, function (key, value) {
                selColModel = value.colmodel;
                item = {};
                item ["id"] = value.id_centro;
                item ["NoProyecto"] = value.nombre;
                item ["centro"] = value.nombre_centro;
                item ["contratante"] = value.contratante;
                item ["tel_contratante"] = value.tel_contratante;
                item ["monto_contratado"] = value.monto_contratado;
                item ["tipo"] = value.tipo;
                item ["presupuesto_original"] = value.presupuesto;
                item ["monto_adendas"] = value.adendas;
                item ["presupuesto_total"] = parseFloat(value.presupuesto)+parseFloat(value.adendas);
                if(!parseFloat(value.adendas))
                    item ["presupuesto_total"] = parseFloat(value.presupuesto); 
                item ["%_de_Avance"] = value.porcentaje_avance;  
                //                item ["%_de_Avance_t"] = value.porcentaje_teorico;  
                item ["gastado"] = value.gastado; 
                var valpresupuestoxgastar = 0;
                    if(presupuestoxgastar === 1){
                        valpresupuestoxgastar = (parseFloat(value.presupuesto)+parseFloat(value.adendas)) - value.gastado; 
                    } else if(presupuestoxgastar === 2){
                        valpresupuestoxgastar = (parseFloat(value.presupuesto)+parseFloat(value.adendas)) - (value.gastado+value.comp); 
                    }
                item ["presupuestoxgastar"] = valpresupuestoxgastar;


                item ["anticipos_por_amortizar"] = value.anticipos_por_amortizar;                
                item ["pagado"] = value.pagado;

                item ["comprometido"]=value.comp;

                item ["facturado"] = value.facturado;
                item ["cobrado"] = value.cobrado;
                item ["update"] = value.ultima_actualizacion;
                item ["level"] = value.nivel;
                item ["parent"] = value.id_padre;
                if(value.id_padre==0)
                    item ["parent"]=null;
                item ["isLeaf"] = value.isleaf;
                item ["expanded"] = false;
                item ["estado"] = value.estado;
                item ["documentacion"] = value.documentacion;

                


                mydata.push(item);
            });


            var colmodel = [
                {name: 'id', index: 'id', width: 100, hidden:true},
                {name: 'NoProyecto', index: 'noproyecto', width: 100, sortable: true, align: 'center',formatter:'showlink', formatoptions:{baseLinkUrl:subfolder +'/details', idName:'idCentro'}},
                //{name: 'centro', index: 'centro', width: 340, sortable: true, align: 'left',formatter:'showlink', formatoptions:{baseLinkUrl:subfolder +'/details', idName:'idCentro'}},
                {name: 'centro', index: 'centro', width: 340, sortable: true, align: 'left'},
                {name: 'contratante', index: 'contratante', width: 180, sortable: true, align: 'center'},
                // {name: 'tel_contratante', index: 'tel_contratante', width: 160, sortable: false, align: 'center'},
                {name: 'tipo', index: 'tipo', width: 120, sortable: false, align: 'center'},
                {name: 'presupuesto_original', index: 'presupuesto_original', width: 180, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                {name: 'monto_adendas', index: 'monto_adendas', width: 100, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                {name: 'presupuesto_total', index: 'presupuesto_total', width: 150, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                {name: '%_de_Avance', width: 120, sorttype: "float", align: 'center', formatter:'currency', formatoptions:{thousandsSeparator: ",", decimalPlaces: 2, suffix: "%"}},
                /*
                    {name: '%_de_Avance_t', width: 150, sorttype: "float", align: 'center', formatter:'currency', formatoptions:{thousandsSeparator: ",", decimalPlaces: 2, suffix: "%"}},
                    */
                {name: 'comprometido', index: 'comprometido', width: 120, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                {name: 'gastado', index: 'gastado', width: 120, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                {name: 'pagado', index: 'pagado', width: 120, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                {name: 'presupuestoxgastar', index: 'presupuestoxgastar', width: 100, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                {name: 'anticipos_por_amortizar', index: 'anticipos_por_amortizar', width: 170, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                {name: 'facturado', index: 'facturado', width: 90, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},
                {name: 'cobrado', index: 'cobrado', width: 90, formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}, sorttype: "float", align: 'right'},                   
                {name: 'update', index: 'update', width: 170, sortable: false, formatter: "date", formatoptions : {srcformat : 'Y-m-d H:i',newformat : 'd-m-Y H:i'},align: 'center'},
                {name: 'documentacion', index: 'documentacion', width: 120, sortable: false, align: 'center',formatter:'showlink', formatoptions:{baseLinkUrl:subfolder +'/documentos', idName:'idCentro'}},
                {name: 'estado', index: 'estado', width: 120, sortable: false, align: 'center',
                 cellattr: function(rowId, val, rawObject) {
                     if (val ==="Activo") {
                         return 'style="background-color:#9ACD32;border-radius:10px;"';
                     }
                     if (val ==="En Revisión") {
                         return 'style="background-color:#FFA500;border-radius:10px;"';
                     }
                     if (val ==="Inactivo") {
                         return 'style="background-color:#A9A9A9;border-radius:10px;"';
                     }
                     if (val ==="Suspendido") {
                         return 'style="background-color:#FF0000;border-radius:10px;"';
                     }
                 }},
                {name: 'name', index: 'name', width: 90, sortable: false, align: 'center'}
            ];

            var colnames = ['id','No. De proyecto','Centro Contable', 'PM', /* 'Tel. del PM', */ 'Tipo', 'Presupuesto Original', 'Adendas', 'Presupuesto Total', '% de avance',/*'% de avance Teorico',*/ 'Comprometido', 'Gastado', 'Desembolsado', 'Presupuesto por gastar', 'Anticipos por Amortizar', 'Facturado', 'Cobrado', 'Última Actualización','Documentacion','Estado', ''];


            if(!anticipopagos){    
                colmodel.removeValue('name','anticipos_por_amortizar')
                colmodel.removeValue('name','pagado')

                var index = colnames.indexOf("Anticipos por Amortizar");
                if (index > -1) {
                    colnames.splice(index, 1);
                }
                index = colnames.indexOf("Desembolsado");
                if (index > -1) {
                    colnames.splice(index, 1);
                }
            }
            if(!estadop){    
                colmodel.removeValue('name','estado')
                var index = colnames.indexOf("Estado");
                if (index > -1) {
                    colnames.splice(index, 1);
                }
            }

            if(!docp){    
                colmodel.removeValue('name','documentacion')
                var index = colnames.indexOf("Documentacion");
                if (index > -1) {
                    colnames.splice(index, 1);
                }
            }

            
            $("#table_list_1").jqGrid({
                regional : 'en',
                data: mydata,
                datatype: "local",
                height: 700,
                autowidth: true,
                shrinkToFit: false,
                rowNum: 10,
                rowList: [10, 20, 30],
                colNames: colnames,
                colModel: colmodel,
                pager: "#pager_list_1",
                viewrecords: true,
                gridComplete: function() {
                    var grid = $("#table_list_1");
                    console.log(grid);
                    var ids = grid.jqGrid('getDataIDs');
                    //var ids = grid.jqGrid('getDataCentros');
                    for (var i = 0; i < ids.length; i++) {
                        var rowId = ids[i];
                        var checkOut = '<a data-toggle="modal" class="btn-primary btn-sm" style="color:white;" onclick="loadModal('+rowId+')">Opciones</a>';
                        var centroLink1 = '<a data-toggle="modal" class="" onclick="centroContableModal('+rowId+')">'; 
                        var centroLink2 = '</a>';           
                        if(grid.jqGrid('getRowData',rowId).isLeaf=='true'){ // only add options to 
                            grid.jqGrid('setRowData', rowId, { name: checkOut });
                            var mycentro = grid.jqGrid('getRowData',rowId).centro;
                            grid.jqGrid('setRowData', rowId, { centro: centroLink1+mycentro+centroLink2 },{background:'white'});
                        }
                        else
                            grid.jqGrid('setRowData', rowId, { estado:'' },{background:'white'});
                        
                    }
                },
                hidegrid: false,
                scrollOffset: 0,
                sortname: 'id',
                treeGrid: true,
                treeGridModel: 'adjacency',
                treedatatype: "local",
                ExpandColumn: 'centro',
                gridview: true
            });
            $.jgrid.formatter.currency.decimalSeparator='.';
            $.jgrid.formatter.currency.thousandsSeparator=',';
            $.jgrid.formatter.currency.decimalPlaces='2';
            //Add responsive to jqGrid
            $(window).bind('resize', function () {
                var width = $('.jqGrid_wrapper').width();
                $('#table_list_1').setGridWidth(width);
            });
            $("#table_list_1")[0].addJSONData({
                total: 1,
                page: 1,
                records: mydata.length,
                rows: mydata
            });
            $("#table_list_1")
                .navGrid('#pager_list_1',{edit:false,add:false,del:false,search:false,refresh:false})
                .navButtonAdd('#pager_list_1',{
                caption: "Expandir / Contraer Todo",
                onClickButton: function() {
                    $("#table_list_1").find(".treeclick").trigger('click');
                },
                position: "last",
                title: "Expandir / Contraer Todo",
                cursor: "pointer"
            });
            $("#table_list_1 tbody .jqgrow").each(function (index){
                if(!(mydata[index]['isLeaf'])) // remove link to totalizadores 
                    $(this).find("a").removeAttr("href").css("cursor","default").css('color', 'black').css('textDecoration','none');
            });
            if($('#verFacturasCobros').val()!=1){ // hide facturado and cobrado to users no allowed
                $("#table_list_1").jqGrid('hideCol',["facturado"]);
                $("#table_list_1").jqGrid('hideCol',["cobrado"]);        
            }        
        }
    });
});

function reload(action){
    if(action == 'clear'){
        $('#textFilter').val('');
        $('#dateFilter').val('');
    }
    $("#modalCargando").modal();
    var mydata = [];
    var token = $('input[name=_token]').val();
    filterText = $('#textFilter').val();
    filterText2 = $('#dateFilter').val();
    $.ajax({
        url: subfolder + '/centros',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data: {'textFilter':filterText,'dateFilter':filterText2,'search':1} ,
        datatype: 'JSON',
        success: function (resp) {
            $('#modalCargando').modal('hide');
            if(resp.centros != null){
                var presupuestoxgastar = parseInt(resp.presupuestoxgastar);
                
                $.each(resp.centros, function (key, value) {
                    selColModel = value.colmodel;

                    item = {}
                    item ["id"] = value.id_centro;
                    item ["NoProyecto"] = value.nombre;
                    item ["centro"] = value.nombre_centro;
                    item ["contratante"] = value.contratante;
                    item ["tel_contratante"] = value.tel_contratante;
                    item ["tipo"] = value.tipo;
                    item ["presupuesto_original"] = value.presupuesto;
                    item ["monto_adendas"] = value.adendas;
                    item ["presupuesto_total"] = parseFloat(value.presupuesto)+parseFloat(value.adendas);
                    if(!parseFloat(value.adendas))
                        item ["presupuesto_total"] = parseFloat(value.presupuesto);
                    item ["%_de_Avance"] = value.porcentaje_avance;
                    //                    item ["%_de_Avance_t"] = value.porcentaje_teorico;
                    item ["gastado"] = value.gastado;
                    var valpresupuestoxgastar = 0;
                    if(presupuestoxgastar === 1){
                        valpresupuestoxgastar = (parseFloat(value.presupuesto)+parseFloat(value.adendas)) - value.gastado; 
                    } else if(presupuestoxgastar === 2){
                        valpresupuestoxgastar = (parseFloat(value.presupuesto)+parseFloat(value.adendas)) - (value.gastado+value.comp); 
                    }
                    item ["presupuestoxgastar"] = valpresupuestoxgastar;
                    item ["anticipos_por_amortizar"] = value.anticipos_por_amortizar;                
                    item ["pagado"] = value.pagado;


                    item ["facturado"] = value.facturado;
                    item ["cobrado"] = value.cobrado;
                    item ["update"] = value.ultima_actualizacion;
                    item ["level"] = value.nivel;
                    item ["parent"] = value.id_padre;
                    if(value.id_padre==0)
                        item ["parent"]=null;
                    item ["isLeaf"] = value.isleaf;
                    item ["expanded"] = false;
                    item ["estado"] = value.estado;
                    item ["documentacion"] = value.documentacion;

                    anticipopagos =value.anticipopagos;
                    estadop =value.estadop;
                    docp =value.docp;
                    item ["expanded"] = false;
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
                jQuery('#table_list_1').jqGrid('clearGridData').trigger('reloadGrid');
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
                        toastr.success(resp.centros.length +' Registro encontrado');
                    if(mydata.length>1)
                        toastr.success(resp.centros.length +' Registros encontrados');
                }, 1300);
                $("#table_list_1")[0].addJSONData({
                    total: 1,
                    page: 1,
                    records: mydata.length,
                    rows: mydata
                });
                $("#table_list_1 tbody .jqgrow").each(function (index){
                    if(!(mydata[index]['isLeaf'])) // remove link to totalizadores 
                        $(this).find("a").removeAttr("href").css("cursor","default").css('color', 'black').css('textDecoration','none');
                });
                if($('#verFacturasCobros').val()!=1){ // hide facturado and cobrado to users no allowed
                    $("#table_list_1").jqGrid('hideCol',["facturado"]);
                    $("#table_list_1").jqGrid('hideCol',["cobrado"]);        
                }
            }
        }
    });
}

function loadModal(rowId){
    var nameProject = jQuery('#table_list_1').jqGrid ('getCell', rowId, 'note');
    $('#nameProject').html('');
    $('#nameProject').append(nameProject);
    $("#detailsLinkModal").attr("href", subfolder +"/details?idCentro="+rowId);
    $("#presupuestoManual").attr("href", subfolder +"/details-edit?idCentro="+rowId);
    $("#graficoLinkModal").attr("href", subfolder +"/grafico?idCentro="+rowId);
    //$("#graficoMLinkModal").attr("href", subfolder +"/grafico_test");
    $("#gastadoLinkModal").attr("href", subfolder +"/gastado?idCentro="+rowId);
    $("#updateLinkModal").attr("href", subfolder +"/update?idCentro="+rowId);
    $("#adendaLinkModal").attr("href", subfolder +"/adenda_crear?idCentro="+rowId);
    $("#bitacoraLinkModal").attr("href", subfolder +"/bitacora?idCentro="+rowId);
    $("#updateDetailsLinkModal").attr("href", subfolder +"/update_details?idCentro="+rowId);
    $("#updateEstadoProyecto").attr("href", subfolder +"/cambiarestado?idCentro="+rowId);
    $("#documentosProyecto").attr("href", $("#documentosProyecto").attr("href")+"?idCentro="+rowId);
    $("#modal-form").modal();
}

function centroContableModal(rowId){

    $("#detailsFinanciera").attr("href", subfolder +"/details?idCentro="+rowId);
    $("#detailsConsumo").attr("href", subfolder +"/presupuestoitems?idCentro="+rowId);
    $("#modal-form-centros").modal();
}





