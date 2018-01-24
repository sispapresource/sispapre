var mydata = [];
var cuentasDivisoras;
var cuentasAuto;
var t_presupuesto_o = t_adendas = t_presupuesto_t = t_comp = t_gastado = t_presupuesto_g = 0;
var lastSel = null;
$(document).ready(function () {
    $("#table_list_2_Presupuesto_original_2").css("background-color","yellow");
    $("#table_list_2_Presupuesto_original_2").css("color","#000000");
    $(".report").on('click',function(e){
        e.preventDefault();
        var href = $(this).attr("href")+"?";
        href+="desde="+$('#dateFilterDesde').val();
        href+="&hasta="+$('#dateFilterHasta').val();
        window.location.href = href;
    });
  
    $("#modalCargando").modal();
    var  token,id_centro;
    token = $('input[name=_token]').val();
    id_centro = $('#idCentro').val();
    $.ajax({
        url: subfolder +"/cuentasDivisoras",
        headers: {'X-CSRF-TOKEN':token},
        type: 'GET',
        datatype: 'JSON',
        success: function (resp){
            cuentasDivisoras = resp;
        }
    });
    $("#agregar_cuenta").autocomplete({
      source: function( request, response ) {
        var busqueda=$("#agregar_cuenta").val();
        $.ajax( {
          url: subfolder +"/cuentasAutocompletar",
          dataType: "text",
          data: {
            'idCentro':id_centro,
            'busqueda': busqueda
          },
          type: 'GET',
          success: function( data ) {
            var object = $.parseJSON(data);
            response( object );
          },
          select: function( event, ui ) {
            alert('ok');
            return false;
          }
        } );
      },
      minLength: 3
    } );        
    $.ajax({
        url: subfolder +'/cuentas',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        datatype: 'JSON',
        data: {'idCentro':id_centro, search:1} ,
        success: function (resp) {
            var idDivisorant ="";
            if(resp.cuentas==null){
                alert('No existen cuentas en Presupuesto y Avance para este proyecto');
                item = {}                 
                item ["id"] = '';
                item ["Código"] = '';
                item ["Cuenta"] = '';
                item ["Presupuesto_original"] = 0;
                item ["Adendas"] = 0;
                item ["Presupuesto_total"] = 0;
                item ["%_de_Avance"] = 0;
                item ["%_de_Avance_t"] = 0;
                item ["Presupuesto_por_avance"] = 0;
                item ["Comp"] = toCurrency(0);
                item ["Gastado"] = toCurrency(0);
                item ["Presupuesto_por_gastar"] = 0;
                item ["Costo_proyectado"] = 0;
                item ["Diferencia_vs_Presupuesto"] = 0;
                item ["level"] = 0;
                item ["parent"] = 0;
                item ["isLeaf"] = 0;
                item ["expanded"] = false;
                mydata.push(item);            
            }
            else
            {
            $.each(resp.cuentas, function (key, value)  {
                item = {}                 
                item ["id"] = value.id_cuenta;
                item ["Código"] = value.id_cuenta;
                item ["Cuenta"] = value.nombre_cuenta;
                item ["Presupuesto_original"] = value.presupuesto;
                item ["Presupuesto_original_2"] = value.presupuesto;
                item ["Adendas"] = value.adendas;
                item ["Presupuesto_total"] = value.presupuesto_total;
                item ["%_de_Avance"] = value.porcentaje_avance;
                item ["%_de_Avance_t"] = value.porcentaje_teorico;
                item ["Presupuesto_por_avance"] = value.presupuestoxavance;
                item ["Comp"] = toCurrency(value.comp);
                item ["Gastado"] = toCurrency(value.gastado);
                item ["Presupuesto_por_gastar"] = value.presupuestoxgastar;
                item ["Costo_proyectado"] = value.costoproyectado;
                item ["Diferencia_vs_Presupuesto"] = value.diferenciavspresupuesto;
                item ["level"] = value.nivel;
                item ["parent"] = value.id_padre;
                item ["isLeaf"] = value.isleaf;
                item ["expanded"] = false;
                if(item ["level"]==0){
                    t_presupuesto_o += item ["Presupuesto_original"];
                    t_adendas += item ["Adendas"];
                    t_presupuesto_t += item ["Presupuesto_total"];
                    t_comp += value.comp;
                    t_gastado += value.gastado;
                    t_presupuesto_g += item ["Presupuesto_por_gastar"];
                }
                
                // console.log(cuentasDivisoras.cuentas);
                if(value.nivel ===1){
                    var idDivisor = value.id_cuenta.replace(value.id_padre,"");
                    idDivisor=value.id_padre+idDivisor.substring(0,1)+"00.";
                    // console.log("ant "+idDivisorant+" act "+idDivisor);
                    if((idDivisor in cuentasDivisoras.cuentas)){
                        if(idDivisorant !== idDivisor){
                            // console.log("se agregará el divisor: "+idDivisor);
                            var div={};
                            div["id"]=idDivisor;
                            div["Código"]=idDivisor;
                            div["Cuenta"] =cuentasDivisoras.cuentas[idDivisor];                    
                            div["parent"] = value.id_padre;
                            mydata.push(div);
                        }
                    }
                    idDivisorant = idDivisor;
                }
                
                mydata.push(item);
            });
        }
            $("#table_list_2").jqGrid({
                data: mydata,
                datatype: "local",
                height: 600,
                autowidth: true,
                shrinkToFit: false,
                rowNum: 10,
                rowList: [10, 20, 30],
                colNames:['id','Codigo','Cuenta','Presupuesto original','Adendas','Presupuesto total','Editar presupuesto original'],
                colModel: [
                    {name: 'id', editable:false, index: 'id', width: 100, hidden:true},
                    {name: 'Código', index: 'Código', editable:false, width: 160, classes: 'strong', align: 'center'},
                    {name: 'Cuenta', index: 'Cuenta', editable:false, width: 210, align: 'left'},
                    {name: 'Presupuesto_original', editable:false, width: 180, align: 'right', formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}},
                    {name: 'Adendas', editable:false, width: 130,align: 'right', formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}},
                    {name: 'Presupuesto_total', editable:false, width: 150, align: 'right', formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}},
                    {name: 'Presupuesto_original_2', editable:true, width: 180, align: 'right', formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}}                    
                ],
                pager: "#pager_list_2",
                viewrecords: true,
                hidegrid: false,
                scrollOffset: 0,
                sortname: 'id',
                treeGrid: true,
                treeGridModel: 'adjacency',
                treedatatype: "local",
                footerrow: true,
                ExpandColumn: 'Código',
                gridComplete: function() {
                    $("#table_list_2_Presupuesto_original_2").css("background-color","yellow");
                    $("#table_list_2_Presupuesto_original_2").css("color","#000000");
                    /*
                    var res = str.substring(str.length-3,str.length);
                    if(res=='00.') alert('ok');
                    */
                    var grid = $("#table_list_2");
                    var ids = grid.jqGrid('getDataIDs');
                    for (var i = 0; i < ids.length; i++) {
                        var rowId = ids[i];
                        var cuenta = grid.jqGrid('getRowData',rowId).Cuenta;
                        var id_cuentas=grid.jqGrid('getRowData',rowId).id;
                        var res = id_cuentas.substring(id_cuentas.length-3,id_cuentas.length);
                        
                        if(res=='00.'){                        
                        //if(cuenta.includes('Divisor')){ // only add options to 
                            grid.jqGrid('setRowData', rowId, false, 'myclass');
                            grid.jqGrid('setRowData', rowId, { 
                                "Cuenta": cuenta,//.replace("Divisor",""), 
                                "Presupuesto_original" : "-",
                                "Adendas" : "-",
                                "Presupuesto_total" : "-",
                                "%_de_Avance" : "-",
                                "%_de_Avance_t" : "-",
                                "Presupuesto_por_avance" : "-",
                                "Comp" : "-",
                                "Gastado" : "-",
                                "Presupuesto_por_gastar" : "-",
                                "Costo_proyectado":"-",
                                "Diferencia_vs_Presupuesto" : "-"
                            });
                        }
                        //else
                        //grid.jqGrid('setRowData', rowId, { estado:'' },{background:'white'});
                        
                    }
                },
                //cellEdit : true,
                //cellsubmit : 'clientArray',
                //editurl: subfolder +'/guardarEdit',
                saveRow:  function(rowId, cellname, value, iRow, iCol){
                    console.log(value);
                    console.log(rowId);
                },
                onSelectRow: function(id) {
                    var grid = $("#table_list_2");
                    if (id && id !== lastSel) {
                        grid.jqGrid('restoreRow',lastSel);
                        var cm = grid.jqGrid('getColProp','Name');
                        var data = grid.jqGrid('getRowData',id)
                        console.log(data);
                        if(data.isLeaf==="true"){
                            cm.editable = false;
                            // grid.jqGrid('editRow', id, true, null, null, 'clientArray');
                            var editparameters = {
                                "keys" : true,
                                "oneditfunc" : null,
                                "successfunc" : null,
                                "url" :subfolder +'/guardarEdit' /* "clientArray" */,
                                "extraparam" : {'_token': $('input[name=_token]').val(),'id_centro':$('#idCentro').val()},
                                "aftersavefunc" : function(rowId,response){
                                    console.log(response);
                                },
                                "errorfunc": null,
                                "afterrestorefunc" : null,
                                "restoreAfterError" : true,
                                "mtype" : "POST"
                            }
                            grid.jqGrid('editRow', id, editparameters);
                            cm.editable = true;
                        }
                        lastSel = id;
                    }
                }
            });
            
            // add totales
            $("#table_list_2_Presupuesto_original").append('<h6>Total: '+toCurrency(t_presupuesto_o)+'</h6>');
            $("#table_list_2_Adendas").append('<h6>Total: '+toCurrency(t_adendas)+'</h6>');
            $("#table_list_2_Presupuesto_total").append('<h6>Total: '+toCurrency(t_presupuesto_t)+'</h6>');
            $("#table_list_2_Comp").append('<h6>Total: '+toCurrency(t_comp)+'</h6>');
            $("#table_list_2_Gastado").append('<h6>Total: '+toCurrency(t_gastado)+'</h6>');
            $("#table_list_2_Presupuesto_por_gastar").append('<h6>Total: '+toCurrency(t_presupuesto_g)+'</h6>');
            // Add responsive to jqGrid
            
            $.jgrid.formatter.currency.decimalSeparator='.';
            $.jgrid.formatter.currency.thousandsSeparator=',';
            $.jgrid.formatter.currency.decimalPlaces='2';
            $(window).bind('resize', function () {
                var width = $('.jqGrid_wrapper').width();
                $('#table_list_2').setGridWidth(width);
            });
            $("#table_list_2")[0].addJSONData({
                total: 1,
                page: 1,
                records: mydata.length,
                rows: mydata
            });
            $("#table_list_2")
            .navGrid('#pager_list_2',{edit:false,add:false,del:false,search:false,refresh:false})
            .navButtonAdd('#pager_list_2',{
                caption: "Expandir / Contraer Todo",
                onClickButton: function() {
                    $("#table_list_2").find(".treeclick").trigger('click');
                },
                position: "last",
                title: "Expandir / Contraer Todo",
                cursor: "pointer"
            });
            
            $("#table_list_2 tbody .jqgrow").each(function (index){
                if($(this).css("display") != "none") // strong to cuentas padre,
                    $(this).children('td:nth-child(3)').addClass('strong'); 
                if((mydata[index]['Diferencia_vs_Presupuesto'])<0) // red to Diferencia_vs_Presupuesto negativo 
                    $(this).children('td:nth-child(13)').css('color', 'red');
                else // green to Diferencia_vs_Presupuesto positivo
                    $(this).children('td:nth-child(13)').css('color', 'green');
            });
            // add totales in footer
            $("#table_list_2").jqGrid('footerData','set', {Cuenta: 'TOTALES:', Presupuesto_original: t_presupuesto_o, Adendas: t_adendas, Presupuesto_total: t_presupuesto_t, Comp: toCurrency(t_comp), Gastado: toCurrency(t_gastado), Presupuesto_por_gastar: t_presupuesto_g});
            // remove links in footer
            $(".footrow").find("a").removeAttr("href").css("cursor","default").css('color', 'white').css('textDecoration','none');
            $('#modalCargando').modal('hide');            
        }
    });
    
});

function reload(id_centro,action){
    if(action == 'clear'){
        $('#inputCodigo').val('');
        $('#inputCuenta').val('');
    }
    $("#modalCargando").modal();
    var mydata = [];
    t_presupuesto_o = t_adendas = t_presupuesto_t = t_comp = t_gastado = t_presupuesto_g = 0;
    var token = $('input[name=_token]').val();
    
    $('#fechaDesde').val($('#dateFilterDesde').val());
    $('#fechaHasta').val($('#dateFilterHasta').val());
    
    
    inputCodigo = $('#inputCodigo').val();
    inputCuenta = $('#inputCuenta').val();
    inputCategoria = $('#inputCategoria').val();
    if(inputCategoria!=null)
        inputCategoria+=',';
    
    $.ajax({
        url:subfolder + '/cuentas',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data: {'codigoFilter':inputCodigo,'cuentaFilter':inputCuenta,'categoriaFilter':inputCategoria, 'dateFilterDesde': $('#dateFilterDesde').val(), 'dateFilterHasta': $('#dateFilterHasta').val(), 'idCentro':id_centro} ,
        datatype: 'JSON',
        success: function (resp) {
            $('#modalCargando').modal('hide');
            if(resp.cuentas==null){
                alert('No existen cuentas en Presupuesto y Avance para este proyecto');
                item = {}                 
                item ["id"] = '';
                item ["Código"] = '';
                item ["Cuenta"] = '';
                item ["Presupuesto_original"] = 0;
                item ["Adendas"] = 0;
                item ["Presupuesto_total"] = 0;
                item ["%_de_Avance"] = 0;
                item ["%_de_Avance_t"] = 0;
                item ["Presupuesto_por_avance"] = 0;
                item ["Comp"] = toCurrency(0);
                item ["Gastado"] = toCurrency(0);
                item ["Presupuesto_por_gastar"] = 0;
                item ["Costo_proyectado"] = 0;
                item ["Diferencia_vs_Presupuesto"] = 0;
                item ["level"] = 0;
                item ["parent"] = 0;
                item ["isLeaf"] = 0;
                item ["expanded"] = false;
                mydata.push(item);            
            }
            else{
                $.each(resp.cuentas, function (key, value) {
                    item = {}                    
                    item ["id"] = value.id_cuenta;
                    item ["Código"] = value.id_cuenta;
                    item ["Cuenta"] = value.nombre_cuenta;
                    item ["Presupuesto_original"] = value.presupuesto;
                    item ["Presupuesto_original_2"] = value.presupuesto;
                    item ["Adendas"] = value.adendas;
                    item ["Presupuesto_total"] = value.presupuesto_total;
                    item ["%_de_Avance"] = value.porcentaje_avance;
                    item ["%_de_Avance_t"] = value.porcentaje_teorico;
                    item ["Presupuesto_por_avance"] = value.presupuestoxavance;
                    item ["Comp"] = toCurrency(value.comp);
                    item ["Gastado"] = toCurrency(value.gastado);
                    item ["Presupuesto_por_gastar"] = value.presupuestoxgastar;
                    item ["Costo_proyectado"] = value.costoproyectado;
                    item ["Diferencia_vs_Presupuesto"] = value.diferenciavspresupuesto;
                    item ["level"] = value.nivel;
                    item ["parent"] = value.id_padre;
                    item ["isLeaf"] = value.isleaf;
                    item ["expanded"] = false;      
                    if(item ["level"]==0){
                        t_presupuesto_o += item ["Presupuesto_original"];
                        t_adendas += item ["Adendas"];
                        t_presupuesto_t += item ["Presupuesto_total"];
                        t_comp += value.comp;
                        t_gastado += value.gastado;
                        t_presupuesto_g += item ["Presupuesto_por_gastar"];
                    }      
                    
                    //console.log(cuentasDivisoras.cuentas);
                    var idDivisorant ="";
                    if(value.nivel ===1){
                        var idDivisor = value.id_cuenta.replace(value.id_padre,"");
                        idDivisor=value.id_padre+idDivisor.substring(0,1)+"00.";
                        // console.log("ant "+idDivisorant+" act "+idDivisor);
                        if((idDivisor in cuentasDivisoras.cuentas)){
                            if(idDivisorant !== idDivisor){
                                //console.log("se agregará el divisor: "+idDivisor);
                                var div={};
                                div["id"]=idDivisor;
                                div["Código"]=idDivisor;
                                div["Cuenta"] =cuentasDivisoras.cuentas[idDivisor]+"Divisor";                    
                                div["parent"] = value.id_padre;
                                mydata.push(div);
                            }
                        }
                        idDivisorant = idDivisor;
                    }
                    
                    
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
                $("#table_list_2_Presupuesto_original h6").html('');
                $("#table_list_2_Adendas h6").html('');
                $("#table_list_2_Presupuesto_total h6").html('');
                $("#table_list_2_Comp h6").html('');
                $("#table_list_2_Gastado h6").html('');
                $("#table_list_2_Presupuesto_por_gastar h6").html('');
                jQuery('#table_list_2').jqGrid('clearGridData').trigger('reloadGrid');
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
                        toastr.success(resp.cuentas.length +' Registro encontrado');
                    if(mydata.length>1)
                        toastr.success(resp.cuentas.length +' Registros encontrados');
                }, 1300);
                // add totales
                $("#table_list_2_Presupuesto_original h6").html('Total: '+toCurrency(t_presupuesto_o));
                $("#table_list_2_Adendas h6").html('Total: '+toCurrency(t_adendas));
                $("#table_list_2_Presupuesto_total h6").html('Total: '+toCurrency(t_presupuesto_t));
                $("#table_list_2_Comp h6").html('Total: '+toCurrency(t_comp));
                $("#table_list_2_Gastado h6").html('Total: '+toCurrency(t_gastado));
                $("#table_list_2_Presupuesto_por_gastar h6").html('Total: '+toCurrency(t_presupuesto_g));
                $("#table_list_2")[0].addJSONData({
                    total: 1,
                    page: 1,
                    records: mydata.length,
                    rows: mydata
                });
                // strong to cuentas padre
                $("#table_list_2 tbody .jqgrow").each(function (index){
                    if($(this).css("display") != "none")
                        $(this).children('td:nth-child(3)').addClass('strong'); 
                    if((mydata[index]['Diferencia_vs_Presupuesto'])<0) // red to Diferencia_vs_Presupuesto negativo 
                        $(this).children('td:nth-child(13)').css('color', 'red');
                    else // green to Diferencia_vs_Presupuesto positivo
                        $(this).children('td:nth-child(13)').css('color', 'green');
                });
                // add totales in footer
                $("#table_list_2").jqGrid('footerData','set', {Cuenta: 'TOTALES:', Presupuesto_original: t_presupuesto_o, Adendas: t_adendas, Presupuesto_total: t_presupuesto_t, Comp: toCurrency(t_comp), Gastado: toCurrency(t_gastado), Presupuesto_por_gastar: t_presupuesto_g});
                // remove links in footer
                $(".footrow").find("a").removeAttr("href").css("cursor","default").css('color', 'white').css('textDecoration','none');
            }
        }
    });
}
$(function () {
    $('select').multipleSelect({selectAll: false,
        countSelected: "# elementos seleccionados",
        minimumCountSelected: 6,
        placeholder: 'Seleccione las cuentas a agregar'});
    });
    function toCurrency(value){
        return "$"+value.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
    }
    function exportView(idCentro){
        $("#modalCargando").modal();
        token = $('input[name=_token]').val();
        id_centro = idCentro;
        $.ajax({
            url: subfolder +"/cuentasDivisoras",
            headers: {'X-CSRF-TOKEN':token},
            type: 'GET',
            datatype: 'JSON',
            success: function (resp){
                cuentasDivisoras = resp;
            }
        });
        $.ajax({
            url: subfolder +'/cuentas',
            headers: {'X-CSRF-TOKEN': token},
            type: 'GET',
            datatype: 'JSON',
            data: {'idCentro':id_centro, search:1} ,
            success: function (resp) {
                var idDivisorant ="";
                if(resp.cuentas==null){
                    alert('No existen cuentas en Presupuesto y Avance para este proyecto');
                    item = {}                 
                    item ["id"] = '';
                    item ["Código"] = '';
                    item ["Cuenta"] = '';
                    item ["Presupuesto_original"] = 0;
                    item ["Adendas"] = 0;
                    item ["Presupuesto_total"] = 0;
                    item ["%_de_Avance"] = 0;
                    item ["%_de_Avance_t"] = 0;
                    item ["Presupuesto_por_avance"] = 0;
                    item ["Comp"] = toCurrency(0);
                    item ["Gastado"] = toCurrency(0);
                    item ["Presupuesto_por_gastar"] = 0;
                    item ["Costo_proyectado"] = 0;
                    item ["Diferencia_vs_Presupuesto"] = 0;
                    item ["level"] = 0;
                    item ["parent"] = 0;
                    item ["isLeaf"] = 0;
                    item ["expanded"] = false;
                    mydata.push(item);            
                }
                else
                {
                $.each(resp.cuentas, function (key, value)  {
                    item = {}                 
                    item ["id"] = value.id_cuenta;
                    item ["Código"] = value.id_cuenta;
                    item ["Cuenta"] = value.nombre_cuenta;
                    item ["Presupuesto_original"] = value.presupuesto;
                    item ["Presupuesto_original_2"] = value.presupuesto;
                    item ["Adendas"] = value.adendas;
                    item ["Presupuesto_total"] = value.presupuesto_total;
                    item ["%_de_Avance"] = value.porcentaje_avance;
                    item ["%_de_Avance_t"] = value.porcentaje_teorico;
                    item ["Presupuesto_por_avance"] = value.presupuestoxavance;
                    item ["Comp"] = toCurrency(value.comp);
                    item ["Gastado"] = toCurrency(value.gastado);
                    item ["Presupuesto_por_gastar"] = value.presupuestoxgastar;
                    item ["Costo_proyectado"] = value.costoproyectado;
                    item ["Diferencia_vs_Presupuesto"] = value.diferenciavspresupuesto;
                    item ["level"] = value.nivel;
                    item ["parent"] = value.id_padre;
                    item ["isLeaf"] = value.isleaf;
                    item ["expanded"] = false;
                    if(item ["level"]==0){
                        t_presupuesto_o += item ["Presupuesto_original"];
                        t_adendas += item ["Adendas"];
                        t_presupuesto_t += item ["Presupuesto_total"];
                        t_comp += value.comp;
                        t_gastado += value.gastado;
                        t_presupuesto_g += item ["Presupuesto_por_gastar"];
                    }
                    
                    // console.log(cuentasDivisoras.cuentas);
                    if(value.nivel ===1){
                        var idDivisor = value.id_cuenta.replace(value.id_padre,"");
                        idDivisor=value.id_padre+idDivisor.substring(0,1)+"00.";
                        // console.log("ant "+idDivisorant+" act "+idDivisor);
                        if((idDivisor in cuentasDivisoras.cuentas)){
                            if(idDivisorant !== idDivisor){
                                // console.log("se agregará el divisor: "+idDivisor);
                                var div={};
                                div["id"]=idDivisor;
                                div["Código"]=idDivisor;
                                div["Cuenta"] =cuentasDivisoras.cuentas[idDivisor];                    
                                div["parent"] = value.id_padre;
                                mydata.push(div);
                            }
                        }
                        idDivisorant = idDivisor;
                    }
                    
                    mydata.push(item);
                }); 
            }
            }       
        });
        jsonString = JSON.stringify(mydata);
        var token = $('input[name=_token]').val();
        $.ajax({
            url: subfolder +'/export_view_detail',
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            data:  {idCentro:idCentro, rows:jsonString},
            success:  function(response){
                $('#modalCargando').modal('hide');
                window.location.href = subfolder + response;
            }
        });
     
    }