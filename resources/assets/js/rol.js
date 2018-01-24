$('body').change('.toggle',function(event){
    var me = $("#"+event.target.id);
    var token = $('input[name=_token]').val();
    if($(me).prop('checked') !== undefined){
        $("#modalCargando").modal();    
        $.ajax({
            url:  subfolder+"/centro_user_update",
            type: "POST",
            data: {
                _token: token,
                user:$('#selectuser').val().split('_')[0],
                centro: $(me).data('idcentro'),
                value: $(me).prop('checked')
            },
            success: function (res) {
                me.parents('td').attr('data-order',$(me).prop('checked')? 1: 0);
                swal(res.titulo, res.msg, "success");
                setTimeout(function(){
                    $('#modalCargando').modal('hide');
                },1000);
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                swal("Ocurrio un error!", "Porfavor intente de nuevo", "error");
                setTimeout(function(){
                    $('#modalCargando').modal('hide');
                },1000);
            }
        });                 
    }
});
var rol_uni=0;
$("#divuser").on("change", "#selectuser", function(){ // Blade Usuarios
    $("#modalCargando").modal();
    $("#div-rol").show();
    $("#div-proyectos").show();
    $('#rol_desc').html('Rol Actual: '+$('#selectuser').val().split('_')[1]);
    $('#details_centros_user').empty();
    var token = $('input[name=_token]').val();
   
    $.ajax({
        url: subfolder +'/centros_user',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data:  {user:$('#selectuser').val().split('_')[0]},
        success:  function(resp){
            $('#modalCargando').modal('hide');
            if(resp.centros_user != null){
                var html='<table class="datatable compact dt-center" cellspacing="0" width="100%">';
                html += '<thead><tr><th>Estado</th><th>Centro</th></tr></thead><tfoot><tr><th>Estado</th><th>Centro</th></tr></tfoot><tbody>';
                $.each(resp.centros_user, function (key, value) {
                    html+="<tr>";
                    html+='<td align="center" data-order="`+value.assign+`"><div class="checkbox"><label><input class="toggle" type="checkbox" id="checkbox-'+value.id+'" data-nombre="'+value.name+'" data-idcentro="'+value.id+'" '+ (value.assign==1? "checked" : "") +' data-toggle="toggle"></label></div></td>';
                    html+="<td>"+value.name+"</td>";
                    html+="</tr>";
                });
                $('#details_centros_user').append(html);
                $('.toggle').bootstrapToggle();
                $('.datatable').DataTable();
                
            }
        }
    });
});
$("#div-rol").on("change", "#tiporol", function(){ // Blade Usuarios
    $("#div-save-usuario-rol").show();
});
$("#div-selectrol").on("change", "#selectrol", function(){ // Blade Roles
    $("#modalCargando").modal();
    $('#details_roles').empty();
    $("#div-guardar-edit").show();
    var token = $('input[name=_token]').val();
    rol_uni=$('#selectrol').val();
    $.ajax({
        url: subfolder +'/permisos',
        headers: {'X-CSRF-TOKEN': token},
        type: 'GET',
        data:  {rol:rol_uni},
        success:  function(resp){
            $('#modalCargando').modal('hide');
            if(resp.permisos != null){
                var html='<div class="form-group col-sm-9"><b>Opción</b></div><div class="form-group col-sm-3"><b>Ver</b></div>';
                var header = "";
                $.each(resp.permisos, function (key, value) {   
                    if(header !== value.description){
                        html+='<h1>'+value.description+'</h1>';
                        header=value.description;
                    }
                    html+='<div class="form-group col-sm-9">';         
                    html+=value.name;
                    html+='</div><div class="form-group col-sm-3 divDetail">';
                    if(value.assign==0){
                        html+=' Si <input type="radio" name="'+value.id+'" value="si">';
                        html+=' No <input type="radio" name="'+value.id+'" value="no" checked="checked"><br>';
                    }
                    else{
                        html+=' Si <input type="radio" name="'+value.id+'" value="si" checked="checked">';
                        html+=' No <input type="radio" name="'+value.id+'" value="no"><br>';
                    }
                    html+='</div>';
                });
                $('#details_roles').append(html);
            }
        }
    });
});
function save(){ // Assign rol to user
    $("#modalCargando").modal();
    var token = $('input[name=_token]').val();
    $("#button-save").prop('disabled', true);
    $.ajax({
        url: subfolder +'/update_rol',
        headers: {'X-CSRF-TOKEN': token},
        type: 'post',
        data:  {user:$('#selectuser').val().split('_')[0],rol:$('#tiporol').val()},
        success:  function(response){
            $('#modalCargando').modal('hide');
            if(response['response']=="success")
                print_message('Rol Actualizado con Exito', subfolder + '/set_rol',1);
            else
                print_message('Actualizacion de Rol Fallida', subfolder +'/set_rol',2);
        }
    });
};
function update_permisos(){ // Update permissions to determinate rol
    $("#modalCargando").modal();
    var updates=[];
    var jsonString, item_a;
    $("input[type=radio]:checked").each(function (index){  
        item_a = {
            id_permiso: $(this).attr('name'),
            value: $(this).val()
        };
        updates.push(item_a);
    }); 
    jsonString = JSON.stringify(updates);
    var token = $('input[name=_token]').val();
    $("#button-save").prop('disabled', true);
    $.ajax({
        url: subfolder +'/permiso_update',
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        data:  {idRol:rol_uni,array:jsonString},
        success:  function(response){
            $('#modalCargando').modal('hide');
            if(response['response']=="success")
                print_message('Permisos Actualizados con Exito',subfolder +'/edit_permisos',1);
            else
                print_message('Actualizacion de Permisos Fallida',subfolder +'/edit_permisos',2);
        }
    });
};
/* function update_centros_user(){ // Update permissions to determinate rol
    $("#modalCargando").modal(); 
    var updates=[];
    var jsonString, item_a;
    $("input[type=radio]:checked").each(function (index){  
        item_a = {
            id_centro: $(this).attr('name'),
            value: $(this).val()
        };
        updates.push(item_a);
    }); 
    jsonString = JSON.stringify(updates);
    var token = $('input[name=_token]').val();
    $("#button-update").prop('disabled', true);
    $.ajax({
        url: subfolder +'/centro_user_update',
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        data:  {idUser:$('#selectuser').val().split('_')[0],array:jsonString},
        success:  function(response){
            $('#modalCargando').modal('hide');
            if(response['response']=="success")
                print_message('Proyectos Actualizados con Exito',subfolder +'/set_rol',1);
            else
                print_message('Actualizacion de Proyectos Fallida',subfolder +'/set_rol',2);
        }
    });
}; */
function print_message(msj,location,type){
    setTimeout(function(){
        toastr.options = {
            closeButton: true,
            progressBar: false,
            positionClass: "toast-top-center",
            showMethod: 'slideDown',
            timeOut: 4000
        };
        if(type==1)
            toastr.success(msj);
        if(type==2)
            toastr.error(msj);
    }, 1300);
    setTimeout(function(){
        window.location=location;
    }, 2600);
}