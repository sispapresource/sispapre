<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Detalle del Ajuste</title>
</head>
<body>

    <style>
    table{
        font-size: 12px;
        text-align: center;
        width: 100%;
        font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif; 
        border-collapse: collapse; 
        margin: 0 auto;
    }
    th {
        border-bottom: 1px solid #fff; 
    }
    td{    
        color: black;     
    }
    tr:hover td { background: #d0dafd; color: #339; }
    .page-break {
        page-break-after: always;
    }
    .title{
        font-weight: 700;
    }
</style>

<main>
    <div id="details" class="clearfix">
        <div id="invoice" style="text-align:center;">
            <h2>DETALLE DEL AJUSTE</h2>
            <h4>Ingeniería R-M, S.A.</h4>
        </div>
    </div>
    <table border="0" cellspacing="0" cellpadding="0" class="table" style="font-size: 12px;">
        <tbody>
            <tr>
                <td class="title">Fecha del reporte:</td>
                <td class="no">{{ $date }}</td>
            </tr>
            <tr>
                <td class="title">Proyecto:</td>
                <td class="no">{{ $centroContable->nombre_centro }}</td>
            </tr>
            <tr>
                <td class="title">P.M.:</td>
                <td class="no">{{ $centroContable->contratante }}</td>
            </tr>
            <tr>
                <td class="title">Adenda</td>
                <td class="no">{{ $adenda->numero }}</td>
            </tr>
            <tr>
                <td class="title">Ajuste</td>
                <td class="no">{{ $ajuste->numero }}</td>
            </tr>
            <tr>
                <td class="title">Fecha del ajuste</td>
                <td class="no">{{ $date_ajuste }}</td>
            </tr>
            <tr>
                <td class="title">Descripcion</td>
                <td class="no">{{ $ajuste->descripcion }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <?php 
    $countt = ceil(count($costos)/45);
    $i=-1;
    for ($k=0; $k<$countt; $k++){ 
    ?>
        <table border="0" cellspacing="0" cellpadding="0" class="table" >
            <thead>
            <tr>
                <th class="no">No. de Item</th>
                <th class="no">Items</th>
                <th class="no">Costo</th>
            </tr>
            </thead>
            <tbody>
                <?php 
                for ($ii=0; $ii<45; $ii++){ 
                    $i++;
                    if(isset($costos[$i])){ 
                        $align='left';
                        if($i>(count($costos)-7)||$costos[$i]['Items']=='Total del item')
                            $align='right';
                        ?>
                            <tr>
                                <td class="no" style="text-align: left;"><?php echo($costos[$i]['No. de item']);?> </td>
                                <td class="no" style="text-align: <?php echo($align)?>;"><?php echo($costos[$i]['Items']);?> </td>
                                <?php if(strlen($costos[$i]['Costo'])>=1){ ?>
                                    <td class="no" style="text-align: right;">$<?php echo number_format(($costos[$i]['Costo']), 2, ',', '.');?> </td>
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

