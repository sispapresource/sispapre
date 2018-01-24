var mydata = [];
var updates = [];
var last = 'tablesd';

$(document).ready(function () {
    $("#modalCargando").modal();
    var token = $('input[name=_token]').val();

    populateSelect("");

    $("#select-tablesd").combobox({ 
        select: function (event, ui) { 
            $("#select-tablesd").trigger('change');
        } 
    });

    function populateSelect(filter){
        $.ajax({
            url: subfolder +'/cuentasu',
            headers: {'X-CSRF-TOKEN': token,cuentaFilter:filter},
            type: 'GET',
            datatype: 'JSON',
            data: {'nivel':2} ,
            success: function (resp) {
                $.each(resp.cuentas, function (key, value) {
                    item = {}
                    item ["id"] = value.id_cuenta;
                    item ["cuenta"] = value.nombre_cuenta;
                    item ["name"] = value.presupuesto;
                    item ["show"] = 1;
                    mydata.push(item);
                    $("#select-tablesd").append('<option value="'+value.id_cuenta+'_'+value.presupuesto+'_'+key+'">'+value.id_cuenta+' '+value.nombre_cuenta+'</option>');
                });
                $('#modalCargando').modal('hide');
            }
        });
    }
    //    $("#select-tablesd").keyup(function(){
    //        $("#select-tablesd").find('option').remove();
    //    });

});



$("#add").click(function(){
    $("#buttonDelete"+last).show();
    var valor1 = Math.floor(Math.random() * 10000);
    $("#adendas").append('<div class="row divDetail" id="div'+valor1+'"><div class="form-group col-sm-3"><label for="inputCuenta" class="btn-block">Cuenta</label><select name="select" class="form-control" id="select-table'+valor1+'"></select></div><div class="form-group col-sm-3" id="data_1"><label for="inputActualizacion" class="btn-block">Monto actual</label><div class="input-group" id="inputActualizacion" ><span class="usd-off input-group-addon "><i class="fa fa-usd"></i></span><input class="form-control amountOld" type="text" disabled="disabled" id="select-table'+valor1+'amountOld"></div></div><div class="col-sm-3 form-group"> <label for="inputCuenta" class="btn-block">Costo</label><div class="input-group date" id="inputActualizacion" ><span class="input-group-addon"><i class="fa fa-usd"></i></span><input class="form-control amountNew" type="text" id="select-table'+valor1+'amountNew"></div></div><div class="col-sm-3 button-top"><button type="button" class="btn btn-gray" id="buttonDeletetable'+valor1+'" style="display:none;" onclick="deleteDiv('+valor1+')"><i class="fa fa-trash-o" title="Delete"></i></button></div></div>');
    $.each(mydata, function (key, value) {
        $("#select-table"+valor1).append('<option value="'+mydata[key]["id"]+'_'+mydata[key]["name"]+'_'+key+'">'+mydata[key]["id"]+' '+mydata[key]["cuenta"]+'</option>');
        $("#select-table"+valor1).addClass('select-table');
    });
    last="table"+valor1;
    $("#select-table"+valor1).combobox({ 
        select: function (event, ui) { 
            $("#select-table"+valor1).trigger('change'); // add function change to news select
        } 
    });
    $("#select-table"+valor1).trigger('change');
});

$("#adendas").on("change", ".select-table", function(){
    $("#divAdenda").show();	
    $("#divamountOld").show();
    $(".btn-add").show();
    $("#"+$(this).attr("id")+"amountOld").val($(this).val().split('_')[1]);
});

$("#adendas").on("keypress", ".amountNew", function(e){
    if (e.which != 45 && e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) // -, retroceso, nulo, punto, numeros
        return false; 
});

$("#adendas").on("keyup", ".amountNew", function(){
    reloadAmountAdenda();    
});

function reloadAmountAdenda() {
    var totalOld = totalNew = 0;
    $(".amountNew").each(function (index){ 
        if($(this).val().trim().length > 0)
            totalNew+=parseFloat($(this).val());
    }); 
    $(".amountOld").each(function (index){ 
        if($(this).val().trim().length > 0)
            totalOld+=parseFloat($(this).val());
    });
    
    var res = (totalNew).toFixed(2)+"";
    if(res === '-0.00'){
        res = '0.00';
    }
    $('#amountAdenda').val(res);
};

function deleteDiv(id) {
    var exist=0;
    $(".select-table").each(function (index){ 
        exist+=1;
    });
    if(exist>1){
        mydata[$("#select-table"+id+"").val().split('_')[2]]["show"]=1;
        $("#select-"+last).empty();
        $.each(mydata, function (key, value){
            if(mydata[key]["show"]==1)
                $("#select-"+last).append('<option value="'+mydata[key]["id"]+'_'+mydata[key]["name"]+'_'+key+'">'+mydata[key]["id"]+' '+mydata[key]["cuenta"]+'</option>');
        });
        $("#div"+id).remove();
        reloadAmountAdenda();
    }
};

function save(idAjuste){
    var updates=[];
    var jsonString, item_a;
    $(".divDetail").each(function (index){ 
        if($(this).find(".form-group .input-group .amountNew").val().trim().length > 0){
            item_a = {
                id_cuenta: $(this).find(".form-group .select-table").val().split('_')[0],
                monto_anterior: $(this).find(".form-group .select-table").val().split('_')[1],
                monto_nuevo: parseFloat($(this).find(".form-group .input-group .amountNew").val())
            };
            updates.push(item_a);        
        }
    }); 
    jsonString = JSON.stringify(updates);
    var token = $('input[name=_token]').val();
    $("#button-save").prop('disabled', true);
    $.ajax({
        url: subfolder +'/item_guardar',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data:  {idAjuste:idAjuste,nro_item:$('#nro_item').val(),observaciones:$('#observaciones').val(),monto:$('#amountAdenda').val(),amounts:jsonString},
        success:  function(response){
            if(response['response']=="success"){
                setTimeout(function(){
                    toastr.options = {
                        closeButton: true,
                        progressBar: false,
                        positionClass: "toast-top-center",
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    toastr.success('Item Creado con Exito');
                }, 1300);
                setTimeout(function(){
                    window.location=subfolder+"/ajuste_detail?idAjuste="+idAjuste;
                }, 2600);    
            }
            else{
                setTimeout(function(){
                    toastr.options = {
                        closeButton: true,
                        progressBar: false,
                        positionClass: "toast-top-center",
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    toastr.error('Creacion de Item Fallida');
                }, 1300);
                setTimeout(function(){
                    window.location=subfolder+"/ajuste_detail?idAjuste="+idAjuste;
                }, 2600);
            }
        }
    });
};

