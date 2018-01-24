<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Example 2</title>
</head>
<body>

    <style>
    table {
        font-size: 12px;
        text-align: center;
        width: 100%;
        font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif; 
        border-collapse: collapse; 
        margin: 0 auto;
    }

    th {
        padding: 9px;
        background: #337ab7;
        border-top: 4px solid #aabcfe;    
        border-bottom: 1px solid #fff; 
        color: white; 
    }
    td{    
        padding: 9px;
        background: #99CCFF;
        border-bottom: 1px solid #fff;
        color: black;    
        border: 1px solid #000; 
    }
    
    .page-break {
        page-break-after: always;
    }
    .title{
        background: #337ab7;
        color: white; 
        font-weight: 700;
    }

</style>

<main>
    <div id="details" class="clearfix">
        <div id="invoice" style="text-align:center;">
            <h2>DETALLE DE ADENDA</h2>
            <h4>Ingeniería R-M, S.A.</h4>
        </div>
    </div>
    <table border="0" cellspacing="0" cellpadding="0" class="table" style="font-size: 12px;">
        <tbody>
            <tr>
                <td class="title">Fecha del reporte:</td>
                <td class="title">Proyecto:</td>
                <td class="title">P.M.:</td>
                <td class="title">Adenda</td>
                <td class="title">Fecha de la adenda</td>
                <td class="title">Estado</td>
                <td class="title">Descripcion</td>
                
            </tr>
            <tr>
                <td class="no">{{ $date }}</td>
                <td class="no">{{ $centroContable->nombre_centro }}</td>
                <td class="no">{{ $centroContable->contratante }}</td>
                <td class="no">{{ $adenda->numero }}</td>
                <td class="no">{{ $date_adenda }}</td>
                <td class="no">{{ $estado_adenda }}</td>
                <td class="no">{{ $adenda->descripcion }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <?php 
    $countt = ceil(count($costos)/35);
    //$countt = ceil(100/50);
    $i=-1;
    for ($k=0; $k<$countt; $k++){ 
    ?>
        <table border="0" cellspacing="0" cellpadding="0" class="table" >
            <thead>
            <tr>
                <th class="no">No. de ajuste</th>
                <th class="no">Ajustes y items</th>
                <th class="no">Costo</th>
                <th class="no">Utilidad</th>
                <th class="no">Administración</th>
                <th class="no">Subtotal</th>
                <th class="no">ITBMS</th>
                <th class="no">Total</th>
            </tr>
            </thead>
            <tbody>
                <?php 
                for ($ii=0; $ii<35; $ii++){ 
                    $i++;
                    if(isset($costos[$i])){ 
                        $align='left';
                        if($costos[$i]['Ajustes y items']=='Total'||$costos[$i]['Ajustes y items']=='Totales del ajuste')
                            $align='right';
                        ?>


                        <tr>
                            <td class="no" style="text-align: left;"><?php echo($costos[$i]['No. de ajuste']);?> </td>
                            <td class="no" style="text-align: <?php echo($align)?>;"><?php echo($costos[$i]['Ajustes y items']);?> </td>
                            <?php if(strlen($costos[$i]['Costo'])>1){ ?>
                                <td class="no" style="text-align: right;">$<?php echo number_format(($costos[$i]['Costo']), 2, ',', '.');?> </td>
                            <?php } 
                            else{ ?>
                                <td class="no" style="text-align: right;"></td>
                            <?php } ?>
                            <?php if(strlen($costos[$i]['Utilidad'])>1){ ?>
                                <td class="no" style="text-align: right;">$<?php echo number_format(($costos[$i]['Utilidad']), 2, ',', '.');?> </td>
                            <?php } 
                            else{ ?>
                                <td class="no" style="text-align: right;"></td>
                            <?php } ?>
                            <?php if(strlen($costos[$i]['Administración'])>1){ ?>
                                <td class="no" style="text-align: right;">$<?php echo number_format(($costos[$i]['Administración']), 2, ',', '.');?> </td>
                            <?php } 
                            else{ ?>
                                <td class="no" style="text-align: right;"></td>
                            <?php } ?>
                            <?php if(strlen($costos[$i]['Subtotal'])>1){ ?>
                                <td class="no" style="text-align: right;">$<?php echo number_format(($costos[$i]['Subtotal']), 2, ',', '.');?> </td>
                            <?php } 
                            else{ ?>
                                <td class="no" style="text-align: right;"></td>
                            <?php } ?>
                            <?php if(strlen($costos[$i]['ITBMS'])>1){ ?>
                                <td class="no" style="text-align: right;">$<?php echo number_format(($costos[$i]['ITBMS']), 2, ',', '.');?> </td>
                            <?php } 
                            else{ ?>
                                <td class="no" style="text-align: right;"></td>
                            <?php } ?>
                            <?php if(strlen($costos[$i]['Total'])>1){ ?>
                                <td class="no" style="text-align: right;">$<?php echo number_format(($costos[$i]['Total']), 2, ',', '.');?> </td>
                            <?php } 
                            else{ ?>
                                <td class="no" style="text-align: right;"></td>
                            <?php } ?>
                        </tr>
                        <?php 
                        } 
                    } ?>
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
