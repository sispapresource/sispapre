<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Example 2</title>
</head>
<body>

    <style>
    table {
        font-size: 9px;
        text-align: center;
        width: 100%;
        font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif; 
        border-collapse: collapse; 
    }

    th {
        padding: 9px;
        /*background: #337ab7;*/
        border-top: 4px solid #000;    
        border-bottom: 1px solid #000; 
        color: black; 
    }
    td{    
        padding: 9px;
        /*background: #99CCFF;*/
        border-bottom: 1px solid #000;
        color: black;    
        border: 1px solid #000; 
    }
    tr:hover td { background: #d0dafd; color: #339; }
    .page-break {
        page-break-after: always;
    }
</style>

<main>
    <div id="details" class="clearfix">
        <div id="invoice" style="text-align:center;">
            <h1>ACTUALIZADO DE COSTOS</h1>
        </div>
    </div>
    <table border="0" cellspacing="0" cellpadding="0" class="table" style="font-size: 12px;">
        <thead>
        <tr>
            <th class="no">Empresa</th>
            <th class="no">Proyecto</th>
            <th class="no">Fecha del reporte</th>
            <th class="no">Generado por</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td class="no">Ingeniería RM</td>
                <td class="no"><?php echo($totales[0][5]);?></td>
                <td class="no"><?php echo($totales[0][0]);?> </td>
                <td class="no"><?php echo($totales[0][1]);?></td>
            </tr>
        </tbody>
    </table>
    <br>
    <?php 
    $countt = ceil(count($costos)/50);
    //$countt = ceil(100/50);
    $i=-1;
    for ($k=0; $k<$countt; $k++){ 
    ?>
        <table border="0" cellspacing="0" cellpadding="0" >
            <thead>
            <tr>
                <th class="no">Codigo</th>
                <th class="no">Cuenta</th>
                <th class="no">Presupuesto</th>
                <th class="no">Adendas</th>
                <th class="no">Presupuesto total</th>
                <th class="no">% de avance</th>
                <th class="no">Presupuesto por avance</th>
                <th class="no">Comprometido</th>
                <th class="no">Gastado</th>
                <th class="no">Diferencia Vs. Presupuesto</th>
            </tr>
            </thead>
            <tbody>
                <?php 
                for ($ii=0; $ii<50; $ii++){ 
                    $i++;
                    if(isset($costos[$i])){ 
                    ?>
                        <tr>
                            <td class="no" style="text-align: left;"><?php echo($costos[$i]['Código']);?> </td>
                            <td class="no" style="text-align: left;"><?php echo($costos[$i]['Cuenta']);?> </td>
                            <td class="no" style="text-align: right;"><?php if(!empty($costos[$i]['Presupuesto'])){ echo '$'.number_format(($costos[$i]['Presupuesto']) , 2, ',', '.'); } ?> </td>
                            <td class="no" style="text-align: right;"><?php if(!empty($costos[$i]['Adendas'])){ echo '$'.number_format(($costos[$i]['Adendas']) , 2, ',', '.');} ?> </td>
                            <td class="no" style="text-align: right;"><?php if(!empty($costos[$i]['Presupuesto Total'])) { echo '$'.number_format(($costos[$i]['Presupuesto Total']) , 2, ',', '.'); } ?> </td>
                            <td class="no"><?php if(!empty($costos[$i]['% de Avance'])) { echo($costos[$i]['% de Avance']); } ?> </td>
                            <td class="no" style="text-align: right;"><?php if(!empty($costos[$i]['Presupuesto por avance'])){ echo '$'.number_format(($costos[$i]['Presupuesto por avance']), 2, ',', '.'); } ?> </td>
                            <td class="no" style="text-align: right;"><?php if(!empty($costos[$i]['Comprometido'])){ echo '$'.number_format(($costos[$i]['Comprometido']), 2, ',', '.'); } ?> </td>
                            <td class="no" style="text-align: right;"><?php if(!empty($costos[$i]['Gastado'])){ echo '$'.number_format(($costos[$i]['Gastado']), 2, ',', '.'); } ?> </td>
                            <td class="no" style="text-align: right;"><?php if(!empty($costos[$i]['Diferencia vs. Presupuesto'])){ echo '$'.number_format(($costos[$i]['Diferencia vs. Presupuesto']), 2, ',', '.'); } ?> </td>
                        </tr>
                    <?php 
                    }     
                }
                        
                if($k+1==$countt){ ?>
                <tr>
                            <td class="no" style=""></td>
                            <td class="no" style="">TOTALES</td>
                            <td class="no" style=" text-align: right;">$<?php echo($totales[0][2]);?></td>
                            <td class="no" style=" text-align: right;">$<?php echo($totales[0][3]);?></td>
                            <td class="no" style=" text-align: right;">$<?php echo($totales[0][4]);?></td>
                            <td class="no" style=""></td>
                            <td class="no" style=" text-align: right;">$<?php echo($totales[0][6]);?></td>
                            <td class="no" style=" text-align: right;">$<?php echo($totales[0][7]);?></td>
                            <td class="no" style=" text-align: right;">$<?php echo($totales[0][8]);?></td>
                            <td class="no" style=""></td>
                        </tr>
                    <?php 
                }
 
                ?>
            </tbody>
        </table>
        <?php 
        if($k+1<$countt){ 
        ?>
            <div class="page-break"></div>
        <?php 
        }
    } 
    ?>


</body>
</html>