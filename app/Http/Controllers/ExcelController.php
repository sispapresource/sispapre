<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Adenda;
use App\Ajuste;
use App\Factura;
use App\Item;
use App\ItemDetalle;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use App\CentroContable;
use App\Cuenta;
use App\OrdenCompra;
use App\Planilla;
use App\PresupuestoAvance;
use App\User;
use Illuminate\Support\Facades\DB;
use Auth;

class ExcelController extends Controller {
    
    /**exportHomeAdenda
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index($tipo, $centro,Request $request){ // exportar presupuesto de proyecto en excel o PDF (Exportar, Imprimir PDF)
        
        
        $dateFilterDesde = '';
        if($request->has('desde'))
        $dateFilterDesde = $request->desde;
        $dateFilterHasta = '';
        if($request->has('hasta'))
        $dateFilterHasta = $request->hasta;
        if($dateFilterDesde=='')
        $dateFilterDesde='12/12/0000';
        if($dateFilterHasta=='')
        $dateFilterHasta='01/01/2100';
        
        
        $table='presupuesto_y_avance';
        $total['Presupuesto']=$total['Adendas']=$total['Presupuesto Total']=$total['Presupuesto por avance']=$total['Comprometido']=$total['Gastado']=$total['Costo Proyectado']=0;
        
        $idCentro = isset($centro)?$centro: '1';
        
        $centroContable = CentroContable::find($idCentro);
        
        $items = null;
        
        $keyOrder = "cast(SUBSTRING_INDEX(SUBSTRING_INDEX(".$table.".id_cuenta, '.', 3 ),'.',-1) as unsigned),
        cast(SUBSTRING_INDEX(SUBSTRING_INDEX(".$table.".id_cuenta, '.', 4 ),'.',-1) as unsigned),
        cast(SUBSTRING_INDEX(SUBSTRING_INDEX(".$table.".id_cuenta, '.', 5 ),'.',-1) as unsigned)";
        
        $query = DB::table($table)->where("id_centro_contable",$idCentro)->orderBy(\DB::raw($keyOrder))->get();

        $divisores = Cuenta::where('categoria','=','Divisor')->get()->sort(function($A, $B){
            return strnatcasecmp($A->id_cuenta, $B->id_cuenta);
        });
        $actual = $divisores->shift();
        $first = true;
        
        $index=0;
        foreach ($query as $cuenta){   
            
            $comp = OrdenCompra::where('id_centro', $idCentro)->where('id_cuenta', $cuenta->id_cuenta)
            ->whereDate('fecha_transaccion','>=', Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d'))
            ->whereDate('fecha_transaccion','<=', Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d'))
            ->sum('monto_compra');
            $gastado = Factura::where('id_centro', $idCentro)->where('id_cuenta', $cuenta->id_cuenta)
            ->whereDate('fecha_transaccion','>=', Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d'))
            ->whereDate('fecha_transaccion','<=', Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d'))
            ->sum('monto');
            $planilla = Planilla::where('id_centro', $idCentro)->where('id_cuenta', $cuenta->id_cuenta)
            ->whereDate('fecha_transaccion','>=', Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d'))
            ->whereDate('fecha_transaccion','<=', Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d'))
            ->sum('monto');
            
            $adendas = app('App\Http\Controllers\AdendaController')->getMontoAdendasByProyectoAndCuenta($idCentro,$cuenta->id_cuenta);
            
            $cuentaEntity = Cuenta::find($cuenta->id_cuenta);
            
            $resp['Código']=$cuenta->id_cuenta;
            $resp['Cuenta']= $cuentaEntity->nombre_cuenta;
            $resp['Presupuesto']=floatval($cuenta->presupuesto);
            $resp['Adendas']=floatval($adendas);
            $resp['Presupuesto Total']=floatval($cuenta->presupuesto+$adendas);
            $resp['% de Avance']= floatval($cuenta->porcentaje_avance);
            $resp['Presupuesto por avance']=floatval($cuenta->presupuesto * ($cuenta->porcentaje_avance / 100 ));
            $resp['Comprometido']=floatval($comp);
            $resp['Gastado']=floatval($gastado+$planilla);
            $resp['Costo Proyectado']= floatval(0);
            if($resp['% de Avance']>0)
                $resp['Costo Proyectado'] = floatval(($resp['Gastado'] / $resp['% de Avance'])*100);
            else
                $resp['Costo Proyectado']=floatval($resp['Presupuesto Total']+$resp['Gastado']);
            $resp['Diferencia vs. Presupuesto']=floatval($resp['Presupuesto Total']-$resp['Costo Proyectado']);            
            $total['Presupuesto'] += floatval($resp['Presupuesto']);
            $total['Adendas'] += floatval($resp['Adendas']);
            $total['Presupuesto Total'] += floatval($resp['Presupuesto Total']);
            $total['Presupuesto por avance'] += floatval($resp['Presupuesto por avance']);                              
            $total['Comprometido'] += floatval($resp['Comprometido']);
            $total['Gastado'] += floatval($resp['Gastado']);
            $total['Costo Proyectado'] += floatval($resp['Costo Proyectado']);
            // dd(strnatcasecmp($cuenta->id_cuenta, $actual->id_cuenta ));
           // dd(strnatcasecmp('1.3.16.900.','1.3.13.801.4.'),strnatcasecmp('1.3.5.300','1.3.1.300'),strnatcasecmp('1.3.1.300','1.3.1.300'));
            
            if($first && strnatcasecmp($actual->id_cuenta, $cuenta->id_cuenta ) <= 0) {
                    $div= array(
                        'Código'=>$actual->id_cuenta,
                        'Cuenta'=>$actual->nombre_cuenta,
                        'Presupuesto'=>"DivDiv",
                        'Adendas'=>'',
                        'Presupuesto Total'=>'',
                        '% de Avance'=>'',
                        'Presupuesto por avance'=>'',
                        'Comprometido'=>'',
                        'Gastado'=>'',
                        'Costo Proyectado'=>'',
                        'Diferencia vs. Presupuesto'=>'',
                    );
                    $items[]=$div;
                    $first = false;
                    $actual = $divisores->shift();
                
            }else if ($actual && strnatcasecmp($actual->id_cuenta, $cuenta->id_cuenta ) <= 0) {

                while($divisores->first() && strnatcasecmp($divisores->first()->id_cuenta, $cuenta->id_cuenta ) <= 0){
                    $actual = $divisores->shift();
                } 
                // if($actual->id_cuenta =="1.3.17.900."){
                //     dd($actual, $cuenta);
                // }
                $act = explode(".",$actual->id_cuenta);
                $cue = explode(".",$cuenta->id_cuenta);
                $same = true;
                for($i=0; $i<3; $i++){
                    if($act[$i]!=$cue[$i]){
                        $same = false;
                        break;
                    }
                }
                if($same){
                    $div= array(
                        'Código'=>$actual->id_cuenta,
                        'Cuenta'=>$actual->nombre_cuenta,
                        'Presupuesto'=>"DivDiv",
                        'Adendas'=>'',
                        'Presupuesto Total'=>'',
                        '% de Avance'=>'',
                        'Presupuesto por avance'=>'',
                        'Comprometido'=>'',
                        'Gastado'=>'',
                        'Costo Proyectado'=>'',
                        'Diferencia vs. Presupuesto'=>'',
                    );
                    $items[]=$div;
                }
                $actual = $divisores->shift();                
            } 
            
            
            
            
            $items[] = $resp;
            $index++;
        }   
        if($tipo == 'excel'){
            
            $items[] = array('','TOTALES',$total['Presupuesto'],$total['Adendas'],$total['Presupuesto Total'],'',$total['Presupuesto por avance'],$total['Comprometido'],$total['Gastado'],$total['Costo Proyectado']);
            
            Excel::create('actualizado_costos', function($excel) use($items,$centroContable,$request)  {
                
                $excel->sheet('Hoja 1', function($sheet) use($items,$centroContable,$request){
                    
                    $date = Carbon::now()->format('d/m/Y');
                    $UserEntity = User::find(auth()->id());
                    
                    $adendas = $items;
                    $sheet->setAutoSize(true);
                    $sheet->mergeCells('C1:K1');
                    $sheet->mergeCells('B2:D2');
                    $sheet->mergeCells('B3:D3');
                    $sheet->mergeCells('H2:J2');
                    $sheet->mergeCells('H3:J3');

                    $sheet->cell('A1', function($cell) {
                        $cell->setValue('Rango de fechas:');
                    });
                    $sheet->cell('B1', function($cell) use($request) {
                        $cell->setValue('De: '.$request->desde.' Hasta: '.$request->hasta);
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('C1', function($cell) {
                        $cell->setValue('Actualizado de costos');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setFontSize(14);
                    });
                    $sheet->cell('B2', function($cell) {
                        $cell->setValue('Ingeniería RM');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('B3', function($cell) use($centroContable) {
                        $cell->setValue($centroContable->nombre_centro);
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('A2', function($cell) {
                        $cell->setValue('Empresa:');
                    });
                    $sheet->cell('A3', function($cell) {
                        $cell->setValue('Proyecto:');
                    });
                    $sheet->cell('G2', function($cell) {
                        $cell->setValue('Fecha del reporte:');
                    });
                    $sheet->cell('G3', function($cell) {
                        $cell->setValue('Generado por:');
                    });
                    $sheet->cell('H2', function($cell) use($date) {
                        $cell->setValue($date);
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('H3', function($cell) use($UserEntity) {
                        $cell->setValue($UserEntity->name);
                        $cell->setAlignment('center');
                    });
                    $sheet->setHeight(4, 30);
                    $sheet->setBorder('A4:K4', 'thin');
                    $sheet->cells('A4:K4', function($cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setBackground('#BDD7EE');
                    });
                    $sheet->cells('C5:E10000', function($cells) {
                        $cells->setAlignment('right');
                    });
                    $sheet->cells('F5:F10000', function($cells) {
                        $cells->setAlignment('center');
                    });
                    $sheet->cells('G5:K10000', function($cells) {
                        $cells->setAlignment('right');
                    });
                    
                    foreach($adendas as $key => $row){
                        if(array_key_exists('Presupuesto',$row) && strcmp($row['Presupuesto'],"DivDiv")==0){
                            $adendas[$key]['Presupuesto']="";
                            $sheet->row($key+5, function($row) {
                                $row->setBackground('#E5E5E5');
                            });
                        }
                    }
                    
                    $sheet->fromArray($adendas,null,'A4');
                    $sheet->setColumnFormat(array(
                        'C5:C10000' => '"$"#,##0.00_-',
                        'D5:D10000' => '"$"#,##0.00_-',
                        'E5:E10000' => '"$"#,##0.00_-',
                        'G5:K10000' => '"$"#,##0.00_-',
                        'F5:F10000' => '0.00"%"',
                    ));
                    
                });
            })->export('xls');
        }
        if($tipo == 'pdf'){
        $divisorAnt="";
        foreach ($query as $cuenta){   
            $cuentas_pdf = DB::table('cuentas')->where('id_cuenta', $cuenta->id_cuenta)->first();
            $padre_pdf = DB::table('cuentas')->where('id_cuenta', $cuentas_pdf->id_padre)->first();
            $divisor_pdf = DB::table('cuentas')->where('id_cuenta','=', substr($padre_pdf->id_cuenta,0,-3).'00.')->first();
            if(!empty($divisor_pdf)){
                $divisor=$divisor_pdf->id_cuenta;
                if($divisorAnt!==$divisor){
                $resp_pdf['Código']=$divisor;
                $resp_pdf['Cuenta']= $divisor_pdf->nombre_cuenta;
                $resp_pdf['Presupuesto']='';
                $resp_pdf['Adendas']='';
                $resp_pdf['Presupuesto Total']='';
                $resp_pdf['% de Avance']= '';
                $resp_pdf['Presupuesto por avance']='';
                $resp_pdf['Comprometido']='';
                $resp_pdf['Gastado']='';
                $resp_pdf['Costo Proyectado']= '';
                $resp_pdf['Diferencia vs. Presupuesto']='';
                $items2[] = $resp_pdf;                 
                }        
            $comp_pdf = OrdenCompra::where('id_centro', $idCentro)->where('id_cuenta', $cuenta->id_cuenta)
            ->whereDate('fecha_transaccion','>=', Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d'))
            ->whereDate('fecha_transaccion','<=', Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d'))
            ->sum('monto_compra');
            $gastado_pdf = Factura::where('id_centro', $idCentro)->where('id_cuenta', $cuenta->id_cuenta)
            ->whereDate('fecha_transaccion','>=', Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d'))
            ->whereDate('fecha_transaccion','<=', Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d'))
            ->sum('monto');
            $planilla_pdf = Planilla::where('id_centro', $idCentro)->where('id_cuenta', $cuenta->id_cuenta)
            ->whereDate('fecha_transaccion','>=', Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d'))
            ->whereDate('fecha_transaccion','<=', Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d'))
            ->sum('monto');
            
            $adendas_pdf = app('App\Http\Controllers\AdendaController')->getMontoAdendasByProyectoAndCuenta($idCentro,$cuenta->id_cuenta);
            
            $cuentaEntity = Cuenta::find($cuenta->id_cuenta);
            
            $resp_pdf['Código']=$cuenta->id_cuenta;
            $resp_pdf['Cuenta']= $cuentaEntity->nombre_cuenta;
            $resp_pdf['Presupuesto']=$cuenta->presupuesto;
            $resp_pdf['Adendas']=$adendas;
            $resp_pdf['Presupuesto Total']=$cuenta->presupuesto+$adendas;
            $resp_pdf['% de Avance']= $cuenta->porcentaje_avance;
            $resp_pdf['Presupuesto por avance']=$cuenta->presupuesto * ($cuenta->porcentaje_avance / 100 );
            $resp_pdf['Comprometido']=$comp_pdf;
            $resp_pdf['Gastado']=$gastado_pdf+$planilla_pdf;
            $resp_pdf['Costo Proyectado']= 0;
            if($resp_pdf['% de Avance']>0){
            $resp_pdf['Costo Proyectado'] = ($resp_pdf['Gastado'] / $resp_pdf['% de Avance'])*100;
            }
            else{
            $resp_pdf['Costo Proyectado']=$resp_pdf['Presupuesto Total']+$resp_pdf['Gastado'];
            $resp_pdf['Diferencia vs. Presupuesto']=$resp_pdf['Presupuesto Total']-$resp_pdf['Costo Proyectado'];            
            $total['Presupuesto'] += $resp_pdf['Presupuesto'];
            $total['Adendas'] += $resp_pdf['Adendas'];
            $total['Presupuesto Total'] += $resp_pdf['Presupuesto Total'];
            $total['Presupuesto por avance'] += $resp_pdf['Presupuesto por avance'];                              
            $total['Comprometido'] += $resp_pdf['Comprometido'];
            $total['Gastado'] += $resp_pdf['Gastado'];
            $total['Costo Proyectado'] += $resp_pdf['Costo Proyectado'];
            }
            $items2[] = $resp_pdf;
            $divisorAnt=$divisor_pdf->id_cuenta;    
            }
            }

            $costos= $items2;
            $totales[] = array(
                Carbon::now()->format('d/m/Y'),
                User::find(auth()->id())->name,
                number_format($total['Presupuesto'], 2, ',', '.'),
                number_format($total['Adendas'], 2, ',', '.'),
                number_format($total['Presupuesto Total'], 2, ',', '.'),
                $centroContable->nombre_centro,
                number_format($total['Presupuesto por avance'], 2, ',', '.'),
                number_format($total['Comprometido'], 2, ',', '.'),
                number_format($total['Gastado'], 2, ',', '.')
            );
            $view =  \View::make('pdf.actualizado', compact('costos','totales'))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('a4', 'landscape');
            return $pdf->stream('reporte.pdf');
        }
    }
    //A recibe la cuenta // B recive el divisor
    public function comparar($A, $B)
    {   
        return strnatcasecmp($A, $B);
        if(strlen($A)>strlen($B)){
            $shortStringA= substr($A, 0, strlen($B));
            
        }
        else{
            $shortStringB = substr($B, 0, strlen($A));
        }
        //dd(substr($B, 0, strlen($A)));
        $ArrayA = explode('.',$A);
        array_pop($ArrayA);
        $ArrayB = explode('.',$B);
        array_pop($ArrayB);
        for($i=count($ArrayB)-1; $i>=0; $i--){
            $valA = intval($ArrayA[$i]);
            $valB = intval($ArrayB[$i]);
            if($valA != $valB && $valA < $valB){
                return false;
            }
        }   
        return true;
    }
/*    public function exportar_adenda_filtrada($centro,$estado,$fechai,$fechaf){ // exportar adenda en excel o PDF (Exportar adenda)

            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->orderBy('adendas.fecha', 'desc')->get();           
        foreach ($adendas as $adenda){
            $addTrue = true;
            if(isset($estadoFilter) && $estadoFilter!= '' && $estadoFilter!= 'todos'){
                if(strrpos($adenda->estado,$estadoFilter)===false)
                $addTrue = false;
            }
            if($addTrue){
                $centroEntity = CentroContable::find($adenda->id_centro);
                $resp['Proyecto']=$centroEntity->nombre_centro;
                $resp['No. de documento']=$adenda->numero;
                $resp['Fecha de doc.']=Carbon::parse($adenda->fecha)->format('d/m/Y');
                $resp['Descripción']=trim($adenda->descripcion);
                $resp['Costo']= floatval(app('App\Http\Controllers\AdendaController')->getCostoByAdenda($adenda->id));
                $resp['Utilidad'] = floatval(app('App\Http\Controllers\AdendaController')->getUtilidadByAdenda($adenda->id));
                $resp['Admin.']= floatval(app('App\Http\Controllers\AdendaController')->getAdministracionByAdenda($adenda->id));
                $resp['Subtotal']= $resp['Costo'] + $resp['Utilidad'] + $resp['Admin.'];
                $resp['ITBMS']= floatval(app('App\Http\Controllers\AdendaController')->getItbmsByAdenda($adenda->id));
                $resp['Total']= $resp['Subtotal'] + $resp['ITBMS'];
                $resp['Estado']=app('App\Http\Controllers\AdendaController')->getEstadobyShort($adenda->estado);
                $items[] = $resp;
                
                $total['Costo'] += $resp['Costo'];
                $total['Utilidad'] += $resp['Utilidad'];
                $total['Admin.'] += $resp['Admin.'];
                $total['Subtotal'] += $resp['Subtotal'];                              
                $total['ITBMS'] += $resp['ITBMS'];
                $total['Total'] += $resp['Total'];
                
                $ajustes = Ajuste::where('id_adenda',$adenda->id)->get();
                
                foreach ($ajustes as $ajuste){   
                    
                    $resp['Proyecto']='';
                    $resp['No. de documento']=$ajuste->numero;
                    $resp['Fecha de doc.']=Carbon::parse($ajuste->fecha)->format('d/m/Y');
                    $resp['Descripción']=trim($ajuste->descripcion);
                    $resp['Costo']= floatval(app('App\Http\Controllers\AdendaController')->getCostoByAjuste($ajuste->id));
                    $resp['Utilidad'] = floatval($ajuste->utilidad);
                    $resp['Admin.']= floatval($ajuste->administracion);
                    $resp['Subtotal']= $resp['Costo'] + $resp['Utilidad'] + $resp['Admin.'];
                    $resp['ITBMS']= floatval($ajuste->itbms);
                    $resp['Total']= $resp['Subtotal'] + $resp['ITBMS'];
                    $resp['Estado']='';
                    
                    $items[] = $resp;
                }
                $items[] = array('Proyecto' => '','No. de documento' => '','Fecha de doc.' => '','Descripción' => '','Costo' => '','Utilidad' => '','Admin.' => '','Subtotal' => '','ITBMS' => '','Total' => '','Estado' => '');
            }
        }
        $items[] = array('','','','TOTALES',$total['Costo'],$total['Utilidad'],$total['Admin.'],$total['Subtotal'],$total['ITBMS'],$total['Total'],'');
        
        $excel = Excel::create('home_adendas', function($excel) use($textFilter,$estadoFilter, $items)  {
            
            $excel->sheet('Hoja 1', function($sheet) use($textFilter,$estadoFilter, $items){
                
                $date = Carbon::now()->format('d/m/Y');
                $UserEntity = User::find(auth()->id());
                
                $sheet->setAutoSize(true);
                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('B3:C3');
                $sheet->mergeCells('B4:C4');
                $sheet->mergeCells('B5:C5');
                // $sheet->mergeCells('E3:J3');
                // $sheet->mergeCells('E4:J4');
                
                $sheet->setColumnFormat(array(
                    'E7:J10000' => '"$"#,##0.00_-',
                ));
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('Adendas de proyectos');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setFontSize(14);
                });
                $sheet->cell('A3', function($cell) {
                    $cell->setValue('Empresa:');
                });
                $sheet->cell('A4', function($cell) {
                    $cell->setValue('Fecha del reporte:');
                });
                $sheet->cell('A5', function($cell) {
                    $cell->setValue('Generado por:');
                });
                $sheet->cell('B3', function($cell) {
                    $cell->setValue('Ingeniería RM');
                    $cell->setAlignment('left');
                });
                $sheet->cell('B4', function($cell) use($date) {
                    $cell->setValue($date);
                    $cell->setAlignment('left');
                });
                $sheet->cell('B5', function($cell) use($UserEntity) {
                    $cell->setValue($UserEntity->name);
                    $cell->setAlignment('left');
                });
                $sheet->cell('D3', function($cell) {
                    $cell->setValue('Centro:');
                    $cell->setAlignment('left');
                });
                $sheet->cell('D4', function($cell) {
                    $cell->setValue('Estado de la adenda:');
                    $cell->setAlignment('left');
                });
                $sheet->cell('E3', function($cell) use($textFilter) {       
                    $cell->setValue($textFilter);
                    if($textFilter=='')
                    $cell->setValue('Todos');
                    $cell->setAlignment('left');
                });
                $sheet->cell('E4', function($cell) use($estadoFilter) {
                    $cell->setValue(app('App\Http\Controllers\AdendaController')->getEstadobyShort($estadoFilter));
                    $cell->setAlignment('left');
                });
                $sheet->setHeight(6, 30);
                $sheet->setBorder('A6:K6', 'thin');
                $sheet->cells('A6:K6', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    //$cells->setBackground('#BDD7EE');
                });
                $sheet->cells('C7:D10000', function($cells) {
                    $cells->setAlignment('center');
                });
                $sheet->cells('E7:J10000', function($cells) {
                    $cells->setAlignment('right');
                });
                $sheet->fromArray($items,null,'A6');

                $sheet->setWidth('A', 30);
                $sheet->setWidth('B', 30);
                $sheet->setWidth('C', 20);
                $sheet->setWidth('D', 50);
                $sheet->setWidth('E', 20);
                $sheet->setWidth('F', 20);
                $sheet->setWidth('G', 20);
                $sheet->setWidth('H', 20);
                $sheet->setWidth('I', 20);
                $sheet->setWidth('J', 20);
                $sheet->setWidth('K', 10);
                $sheet->getStyle('D6:D'. $sheet->getHighestRow())
                ->getAlignment()->setWrapText(true); 
                
            });
        })->export('xls');
    }*/
    public function exportar_actual_adendas(Request $request){
        $adendas = Adenda::query();
        //filtro de permisos
        $adendas = $adendas->whereIn('id_centro', Auth::user()->centros->pluck('id_centro'));
        if ($request->has('centro')&& $request->centro!="") {
            $id =  CentroContable::where('nombre_centro', 'like', "%".$request->centro."%")->pluck('id_centro');
            $adendas = $adendas->whereIn('id_centro', $id->toArray());
        }

        if ($request->has('estado')&& $request->estado!="todos") {
            $adendas = $adendas->where('estado', $request->estado);
        }
        
        if ($request->has('fechai')&& $request->fechai!="") {
            //return Carbon::createFromFormat('m/d/Y',$request->fechai)->format('Y-m-d');
            //return Adenda::where('fecha', '>=', Carbon::createFromFormat('m/d/Y',$request->fechai))->pluck('id');
            $adendas = $adendas->where('fecha', '>=', Carbon::createFromFormat('m/d/Y',$request->fechai)->format('Y-m-d').' 00:00:00');
        }

        if ($request->has('fechaf')&& $request->fechaf!="") {
            $adenas = $adendas->where('fecha', '<=', Carbon::createFromFormat('m/d/Y',$request->fechaf)->format('Y-m-d').' 00:00:00');
        }

        if($request->has('numero')&& $request->numero!=""){
            $adendas = $adendas->where('numero','like','%'. $request->numero.'%');
        }
        

        $items = null;
        $total = null;
        $adendas = $adendas->get();
        // dd($adendas[0]);
        $total['Costo']=0;
        $total['Utilidad']=0;
        $total['Admin.']=0;
        $total['Subtotal']=0;
        $total['ITBMS']=0;
        $total['Total']=0;
        foreach ($adendas as $adenda){
            
            $centroEntity = CentroContable::find($adenda->id_centro);
            $resp['Proyecto']=$centroEntity->nombre_centro;
            $resp['No. de documento']=$adenda->numero;
            $resp['No. de Ajustes']=$adenda->ajustes->pluck('numero')->implode('|');
            $resp['Fecha de doc.']=Carbon::parse($adenda->fecha)->format('d/m/Y');
            $resp['Descripción']=trim($adenda->descripcion);
            $resp['Costo']= floatval(app('App\Http\Controllers\AdendaController')->getCostoByAdenda($adenda->id));
            $resp['Utilidad'] = floatval(app('App\Http\Controllers\AdendaController')->getUtilidadByAdenda($adenda->id));
            $resp['Admin.']= floatval(app('App\Http\Controllers\AdendaController')->getAdministracionByAdenda($adenda->id));
            $resp['Subtotal']= floatval($resp['Costo'] + $resp['Utilidad'] + $resp['Admin.']);
            $resp['ITBMS']= floatval(app('App\Http\Controllers\AdendaController')->getItbmsByAdenda($adenda->id));
            $resp['Total']= floatval($resp['Subtotal'] + $resp['ITBMS']);
            $resp['Estado']=app('App\Http\Controllers\AdendaController')->getEstadobyShort($adenda->estado);
            $items[] = $resp;
            
            $total['Costo'] += $resp['Costo'];
            $total['Utilidad'] += $resp['Utilidad'];
            $total['Admin.'] += $resp['Admin.'];
            $total['Subtotal'] += $resp['Subtotal'];                              
            $total['ITBMS'] += $resp['ITBMS'];
            $total['Total'] += $resp['Total'];
        }
        $items[] = array('','','','','TOTALES',$total['Costo'],$total['Utilidad'],$total['Admin.'],$total['Subtotal'],$total['ITBMS'],$total['Total'],'');
        $parametros = $request->input();
        $excel = Excel::create('home_adendas', function($excel) use($items, $parametros)  {
            
            $excel->sheet('Hoja 1', function($sheet) use($items, $parametros){
                
                $date = Carbon::now()->format('d/m/Y');
                $UserEntity = User::find(auth()->id());
                
                $sheet->setAutoSize(true);
                $sheet->mergeCells('A1:L1');
                $sheet->mergeCells('B3:C3');
                $sheet->mergeCells('B4:C4');
                $sheet->mergeCells('B5:C5');
                // $sheet->mergeCells('E3:J3');
                // $sheet->mergeCells('E4:J4');
                
                $sheet->setColumnFormat(array(
                    'F7:L10000' => '"$"#,##0.00_-',
                ));
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('Adendas de proyectos');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setFontSize(14);
                });
                $sheet->cell('A3', function($cell) {
                    $cell->setValue('Empresa:');
                });   
                $sheet->cell('A4', function($cell) {
                    $cell->setValue('Fecha del reporte:');
                });
                $sheet->cell('A5', function($cell) {
                    $cell->setValue('Generado por:');
                });
                $sheet->cell('B3', function($cell) {
                    $cell->setValue('Ingeniería RM');
                    $cell->setAlignment('left');
                });
                $sheet->cell('B4', function($cell) use($date) {
                    $cell->setValue($date);
                    $cell->setAlignment('left');
                });
                $sheet->cell('B5', function($cell) {
                    $cell->setValue(Auth::User()->name);
                    $cell->setAlignment('left');
                });
                $sheet->cell('D3', function($cell) {
                    $cell->setValue('Centro:');
                    $cell->setAlignment('left');
                });
                $sheet->cell('D4', function($cell) {
                    $cell->setValue('Estado de la adenda:');
                    $cell->setAlignment('left');
                });         
                $sheet->cell('E3', function($cell) use($parametros) {       
                    $cell->setValue($parametros['centro']);
                    if($parametros['centro']=='')
                    $cell->setValue('Todos');
                    $cell->setAlignment('left');
                });
                $sheet->cell('E4', function($cell) use($parametros) {
                    $cell->setValue(app('App\Http\Controllers\AdendaController')->getEstadobyShort($parametros['estado']));
                    $cell->setAlignment('left');
                });
                $sheet->setHeight(6, 30);
                $sheet->setBorder('A6:L6', 'thin');
                $sheet->cells('A6:L6', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    //$cells->setBackground('#BDD7EE');
                });
                $sheet->cells('C7:E10000', function($cells) {
                    $cells->setAlignment('center');
                });
                $sheet->cells('F7:L10000', function($cells) {
                    $cells->setAlignment('right');
                });
                $sheet->fromArray($items,null,'A6');

                $sheet->setWidth('A', 30);
                $sheet->setWidth('B', 30);
                $sheet->setWidth('C', 20);
                $sheet->setWidth('D', 50);
                $sheet->setWidth('E', 20);
                $sheet->setWidth('F', 20);
                $sheet->setWidth('G', 20);
                $sheet->setWidth('H', 20);
                $sheet->setWidth('I', 20);
                $sheet->setWidth('J', 20);
                $sheet->setWidth('K', 10);
                $sheet->setWidth('L', 10);
                $sheet->getStyle('D6:D'. $sheet->getHighestRow())
                ->getAlignment()->setWrapText(true); 
                
            });
        })->export('xls');
        
    }

    public function exportAdenda($tipo, $adenda){ // exportar adenda en excel o PDF (Exportar adenda)
        
        $date = Carbon::now()->format('d/m/Y h:i:s A');
        
        $idAdenda = isset($adenda)?$adenda: '1';
        $adenda = Adenda::find($idAdenda);
        $estado_adenda=app('App\Http\Controllers\AdendaController')->getEstadobyShort($adenda->estado);
        $centroContable = CentroContable::find($adenda->id_centro);
        
        $fields = null;
        
        $ajustes = Ajuste::where('id_adenda',$idAdenda)->get(); // falta filtrar
        
        foreach ($ajustes as $ajuste){   
            
            $resp['No. de ajuste'] = $ajuste->numero;
            $resp['Ajustes y items'] = $ajuste->descripcion;
            $resp['Costo'] = '';
            $resp['Utilidad'] = '';
            $resp['Administración'] = '';
            $resp['Subtotal'] = '';
            $resp['ITBMS'] = '';
            $resp['Total'] = '';
            
            $fields[] = $resp;
            
            $items = Item::where('id_ajuste',$ajuste->id)->get();
            
            foreach ($items as $item){ 
                $fields[] = array('No. de ajuste' => '','Ajustes y items' => $item->numero,'Costo' => floatval($item->monto),'Utilidad' => '','Administración' => '','Subtotal' => '','ITBMS' => '','Total' => ''); 
            }
            
            $fields[] = array('No. de ajuste' => '',
            'Ajustes y items' => 'Totales del ajuste',
            'Costo' => floatval(app('App\Http\Controllers\AdendaController')->getCostoByAjuste($ajuste->id)),
            'Utilidad' => floatval($ajuste->utilidad),
            'Administración' => floatval($ajuste->administracion),
            'Subtotal' => app('App\Http\Controllers\AdendaController')->getCostoByAjuste($ajuste->id)+
            $ajuste->utilidad+
            $ajuste->administracion,
            'ITBMS' => floatval($ajuste->itbms),
            'Total' => app('App\Http\Controllers\AdendaController')->getCostoByAjuste($ajuste->id)+
            $ajuste->utilidad+
            $ajuste->administracion+
            $ajuste->itbms
        );
        if($tipo == 'excel')
        $fields[] = array('No. de ajuste' => '','Ajustes y items' => '','Costo' => '','Utilidad' => '','Administración' => '','Subtotal' => '','ITBMS' => '','Total' => '');
        if($tipo == 'pdf'){
            $fields[] = array('No. de ajuste' => '&nbsp;','Ajustes y items' => '','Costo' => '','Utilidad' => '','Administración' => '','Subtotal' => '','ITBMS' => '','Total' => '');    
        }
    }
    
    $fields[] = array('No. de ajuste' => '',
    'Ajustes y items' => 'Total',
    'Costo' => floatval(app('App\Http\Controllers\AdendaController')->getCostoByAdenda($idAdenda)),
    'Utilidad' => floatval(app('App\Http\Controllers\AdendaController')->getUtilidadByAdenda($idAdenda)),
    'Administración' => floatval(app('App\Http\Controllers\AdendaController')->getAdministracionByAdenda($idAdenda)),
    'Subtotal' => app('App\Http\Controllers\AdendaController')->getCostoByAdenda($idAdenda)+
    app('App\Http\Controllers\AdendaController')->getUtilidadByAdenda($idAdenda)+
    app('App\Http\Controllers\AdendaController')->getAdministracionByAdenda($idAdenda),
    'ITBMS' => floatval(app('App\Http\Controllers\AdendaController')->getItbmsByAdenda($idAdenda)),
    'Total' => app('App\Http\Controllers\AdendaController')->getCostoByAdenda($idAdenda)+
    app('App\Http\Controllers\AdendaController')->getUtilidadByAdenda($idAdenda)+
    app('App\Http\Controllers\AdendaController')->getAdministracionByAdenda($idAdenda)+
    app('App\Http\Controllers\AdendaController')->getItbmsByAdenda($idAdenda)
);

if($tipo == 'excel'){
    
    Excel::create('adenda_detalle', function($excel) use($fields,$centroContable,$adenda,$date,$estado_adenda)  {
        
        $excel->sheet('Hoja 1', function($sheet) use($fields,$centroContable,$adenda,$date,$estado_adenda){
            
            $adendas = $fields;
            $sheet->setAutoSize(true);
            $sheet->mergeCells('A1:H1');
            $sheet->mergeCells('A2:H2');
            $sheet->setColumnFormat(array(
                'C10:H10000' => '"$"#,##0.00'
            ));
            $sheet->cell('A1', function($cell) {
                $cell->setValue('Detalle de adenda');
                $cell->setFontWeight('bold');
                $cell->setAlignment('center');
                $cell->setFontSize(14);
            });
            $sheet->cell('A2', function($cell) {
                $cell->setValue('Ingeniería R-M, S.A.');
                $cell->setAlignment('center');
            });
            $sheet->cell('B3', function($cell) use($date) {
                $cell->setValue($date);
                $cell->setAlignment('left');
            });
            $sheet->cell('B4', function($cell) use($centroContable) {
                $cell->setValue($centroContable->nombre_centro);
                $cell->setAlignment('left');
            });
            $sheet->cell('B5', function($cell) use($adenda) {
                $cell->setValue($adenda->numero);
                $cell->setAlignment('left');
            });
            $sheet->cell('B6', function($cell) use($estado_adenda) {
                $cell->setValue($estado_adenda);
                $cell->setAlignment('left');
            });
            $sheet->cell('B7', function($cell) use($adenda) {
                $cell->setValue($adenda->descripcion);
                $cell->setAlignment('left');
            });
            $sheet->cell('A3', function($cell) {
                $cell->setValue('Fecha del reporte:');
            });
            $sheet->cell('A4', function($cell) {
                $cell->setValue('Proyecto:');
            });
            $sheet->cell('A5', function($cell) {
                $cell->setValue('Adenda:');
            });
            $sheet->cell('A6', function($cell) {
                $cell->setValue('Estado:');
            });
            $sheet->cell('A7', function($cell) {
                $cell->setValue('Descripcion:');
            });
            $sheet->cell('G2', function($cell) {
                $cell->setValue('Fecha del reporte:');
            });
            $sheet->cell('E4', function($cell) {
                $cell->setValue('P.M.:');
            });
            $sheet->cell('E5', function($cell) {
                $cell->setValue('Fecha de la adenda:');
            });
            $sheet->cell('F4', function($cell) use($centroContable) {
                $cell->setValue($centroContable->contratante);
                $cell->setAlignment('left');
            });
            $sheet->cell('F5', function($cell) use($adenda) {
                $cell->setValue(Carbon::parse($adenda->fecha)->format('d/m/Y'));
                $cell->setAlignment('left');
            });
            $sheet->cell('H2', function($cell) use($date) {
                $cell->setValue($date);
                $cell->setAlignment('left');
            });
            $sheet->setHeight(9, 30);
            $sheet->setBorder('A9:H9', 'thin');
            $sheet->cells('A9:H9', function($cells) {
                $cells->setAlignment('center');
                $cells->setValignment('center');
                $cells->setBackground('#BDD7EE');
            });
            $sheet->cells('C10:H10000', function($cells) {
                $cells->setAlignment('right');
            });
            $sheet->fromArray($adendas,null,'A9');
            
        });
    })->export('xls');
}
if($tipo == 'pdf'){
    $costos= $fields;
    $date_adenda=Carbon::parse($adenda->fecha)->format('d/m/Y');
    $view =  \View::make('pdf.adenda', compact('costos','date','centroContable','adenda','date_adenda','estado_adenda'))->render();
    $pdf = \App::make('dompdf.wrapper');
    $pdf->loadHTML($view)->setPaper('a4', 'landscape');
    return $pdf->stream('adenda_detalle.pdf');
}
}
public function exportAjuste($tipo, $ajuste){ // exportar ajuste en excel o PDF (Exportar ajuste)
    
    $date = Carbon::now()->format('d/m/Y h:i:s A');
    
    $idAjuste = isset($ajuste)?$ajuste: '1';
    $ajuste = Ajuste::find($idAjuste);
    $adenda = Adenda::find($ajuste->id_adenda);
    
    $centroContable = CentroContable::find($adenda->id_centro);
    
    $fields = null;
    
    $items = Item::where('id_ajuste',$idAjuste)->get();
    
    foreach ($items as $item){   
        
        $resp['No. de item'] = $item->numero;
        $resp['Items'] = $item->descripcion;
        $resp['Costo'] = '';
        
        $fields[] = $resp;
        
        $items_detalle = ItemDetalle::where('id_item',$item->id)->get();
        
        foreach ($items_detalle as $item_detalle){ 
            $cuenta = Cuenta::find($item_detalle->id_cuenta);
            $fields[] = array('No. de item' => '','Items' =>  $item_detalle->id_cuenta .' '. $cuenta->nombre_cuenta, 'Costo' => floatval($item_detalle->monto_nuevo));
        }
        $fields[] = array('No. de item' => '','Items' => 'Total del item', 'Costo' => floatval($item->monto));
        
        if($tipo == 'excel')
        $fields[] = array('No. de item' => '','Items' => '','Costo' => '');
        if($tipo == 'pdf')
        $fields[] = array('No. de item' => '&nbsp;','Items' => '','Costo' => '');
    }
    
    $fields[] = array('No. de item' => '','Items' => 'Total de costos del ajuste:','Costo' => floatval(app('App\Http\Controllers\AdendaController')->getCostoByAjuste($ajuste->id)));
    $fields[] = array('No. de item' => '','Items' => 'Utilidad:','Costo' => floatval($ajuste->utilidad));
    $fields[] = array('No. de item' => '','Items' => 'Administración:','Costo' => floatval($ajuste->administracion));
    $fields[] = array('No. de item' => '','Items' => 'Subtotal:','Costo' => app('App\Http\Controllers\AdendaController')->getCostoByAjuste($ajuste->id)+$ajuste->utilidad+$ajuste->administracion);
    $fields[] = array('No. de item' => '','Items' => 'ITBMS:','Costo' => floatval($ajuste->itbms));
    $fields[] = array('No. de item' => '','Items' => 'Total:','Costo' => app('App\Http\Controllers\AdendaController')->getCostoByAjuste($ajuste->id)+$ajuste->utilidad+$ajuste->administracion+$ajuste->itbms);
    
    if($tipo == 'excel'){
        
        Excel::create('ajuste_detalle', function($excel) use($fields,$centroContable,$ajuste,$adenda,$date)  {
            
            $excel->sheet('Hoja 1', function($sheet) use($fields,$centroContable,$ajuste,$adenda,$date){
                
                $adendas = $fields;
                $sheet->setAutoSize(true);
                $sheet->mergeCells('A1:C1');
                $sheet->mergeCells('A2:C2');
                $sheet->setColumnFormat(array(
                    'C10:C10000' => '"$"#,##0.00'
                ));
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('Detalle del ajuste');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setFontSize(14);
                });
                $sheet->cell('A2', function($cell) {
                    $cell->setValue('Ingeniería R-M, S.A.');
                    $cell->setAlignment('center');
                });
                $sheet->cell('B3', function($cell) use($date) {
                    $cell->setValue($date);
                    $cell->setAlignment('left');
                });
                $sheet->cell('B4', function($cell) use($centroContable) {
                    $cell->setValue($centroContable->nombre_centro);
                    $cell->setAlignment('left');
                });
                $sheet->cell('B5', function($cell) use($centroContable) {
                    $cell->setValue($centroContable->contratante);
                    $cell->setAlignment('left');
                });
                $sheet->cell('B6', function($cell) use($adenda) {
                    $cell->setValue($adenda->numero);
                    $cell->setAlignment('left');
                });
                $sheet->cell('B7', function($cell) use($ajuste) {
                    $cell->setValue($ajuste->numero);
                    $cell->setAlignment('left');
                });
                $sheet->cell('B8', function($cell) use($ajuste) {
                    $cell->setValue(Carbon::parse($ajuste->fecha)->format('d/m/Y'));
                    $cell->setAlignment('left');
                });
                $sheet->cell('B9', function($cell) use($ajuste) {
                    $cell->setValue($ajuste->descripcion);
                    $cell->setAlignment('left');
                });
                $sheet->cell('A3', function($cell) {
                    $cell->setValue('Fecha del reporte:');
                });
                $sheet->cell('A4', function($cell) {
                    $cell->setValue('Proyecto:');
                });
                $sheet->cell('A5', function($cell) {
                    $cell->setValue('P.M.:');
                });
                $sheet->cell('A6', function($cell) {
                    $cell->setValue('Adenda:');
                });
                $sheet->cell('A7', function($cell) {
                    $cell->setValue('Ajuste:');
                });
                $sheet->cell('A8', function($cell) {
                    $cell->setValue('Fecha del ajuste:');
                });
                $sheet->cell('A9', function($cell) {
                    $cell->setValue('Descripcion:');
                });
                
                $sheet->setHeight(11, 30);
                $sheet->setBorder('A11:C11', 'thin');
                $sheet->cells('A11:C11', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                });
                $sheet->cells('C12:C10000', function($cells) {
                    $cells->setAlignment('right');
                });
                $sheet->fromArray($adendas,null,'A11');
                
            });
        })->export('xls');
    }
    if($tipo == 'pdf'){
        $costos=$fields;
        $date_ajuste=Carbon::parse($ajuste->fecha)->format('d/m/Y');
        $view =  \View::make('pdf.ajuste', compact('costos','date','centroContable','adenda','ajuste','date_ajuste'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('ajuste_detalle.pdf');
    }
}
public function exportDetalleComprometido($centro, $idCuenta, $categoriaFilter, $dateFilterDesde, $dateFilterHasta, $montoFilterDesde, $montoFilterHasta, $proveedorFilter){ // exportar en vista de gastado el detalle de gastado
    
    $date = Carbon::now()->format('d/m/Y');
    
    $idCentro = isset($centro)?$centro: '1';
    if($idCuenta=='null')
    $idCuenta = [''];
    else
    $idCuenta = explode(',',$idCuenta);
    if($categoriaFilter=='null')
    $categoriaFilter = [''];
    else
    $categoriaFilter = array_filter(explode(',',$categoriaFilter));
    
    $centroContable = CentroContable::find($idCentro);
    
    $fields = null;
    
    $total = 0;
    
    $keyOrder = "cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 3 ),'.',-1) as unsigned),
    cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 4 ),'.',-1) as unsigned),
    cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 5 ),'.',-1) as unsigned)";
    
    $cuentas = DB::table('cuentas')
    ->select('cuentas.id_cuenta','cuentas.nombre_cuenta','cuentas.categoria')
    ->join('ordenes_de_compra', 'cuentas.id_cuenta', '=', 'ordenes_de_compra.id_cuenta')
    ->where('ordenes_de_compra.id_centro',$idCentro)
    ->where(function($q) use ($idCuenta){
        foreach($idCuenta as $value){
            $q->orWhere('cuentas.id_cuenta', 'LIKE', $value.'%');
        }
    })
    ->where(function($q) use ($categoriaFilter){
        foreach($categoriaFilter as $value){
            $q->orWhere('cuentas.categoria', 'LIKE', '%'.$value.'%');
        }
    })
    ->distinct('cuentas.id_cuenta')->get();
    
    
    foreach ($cuentas as $cuenta){ 
        
        $ordenes = OrdenCompra::where('id_cuenta', $cuenta->id_cuenta)->where('id_centro',$idCentro)->get();  
        
        $totalcuenta=0;
        
        $first=true;
        $firstTotalCuenta=false;
        
        foreach ($ordenes as $orden){ 
            $add=true;
            if($proveedorFilter != 'null'){
                if(stripos($orden->nombre_proveedor,$proveedorFilter)===false)
                $add = false;
            }
            if($dateFilterDesde!= 'null'){
                $date_init = strtotime($dateFilterDesde);
                $ua_comp = strtotime(isset($orden->fecha_transaccion)? $orden->fecha_transaccion:'1999-01-01 00:00:00');
                if($ua_comp < $date_init)
                $add = false;
            }
            if($dateFilterHasta!= 'null'){
                $date_init = strtotime($dateFilterHasta);
                $ua_comp = strtotime(isset($orden->fecha_transaccion)? $orden->fecha_transaccion:'1999-01-01 00:00:00');
                if($ua_comp > $date_init)
                $add = false;
            }
            if($montoFilterDesde!= 'null'){
                if($montoFilterDesde > $orden->monto_compra)
                $add = false;
            }
            if($montoFilterHasta!= 'null'){
                if($montoFilterHasta < $orden->monto_compra)
                $add = false;
            }
            if($add){
                if($first){
                    $resp['Categoría'] = $cuenta->id_cuenta.' '.$cuenta->nombre_cuenta;
                    $resp['Fecha'] = '';
                    $resp['Descripción'] = '';
                    $resp['Monto'] = '';
                    $resp['Orden'] = '';
                    // $resp['Proveedor'] = '';
                    
                    $fields[] = $resp;
                    $first=false;
                    $firstTotalCuenta=true;
                }
                $fields[] = array('Categoría' => $cuenta->categoria,'Fecha' => Carbon::parse($orden->fecha_transaccion)->format('d/m/Y'), 'Descripción' => $orden->desc_transaccion,'Monto' => floatval($orden->monto_compra),'Orden' => $orden->num_oc/* ,'Proveedor' => 'Recursos Humanos' */);
                $total+=$orden->monto_compra;
                $totalcuenta+=$orden->monto_compra;
            }
        }
        if($firstTotalCuenta){
            $fields[] = array('Categoría' => '','Fecha' => '','Descripción' => 'Total','Monto' => $totalcuenta,'Orden' => ''/* ,'Proveedor' => '' */);
            $fields[] = array('Categoría' => '','Fecha' => '','Descripción' => '','Monto' => '','Orden' => ''/* ,'Proveedor' => '' */);
            $firstTotalCuenta=false;
        }
    }
    
    $fields[] = array('Categoría' => '','Fecha' => '','Descripción' => 'Total general:' ,'Monto' => $total,'Orden' => ''/* ,'Proveedor' => '' */);
    
    Excel::create('detalle_comprometido', function($excel) use($fields,$centroContable,$date)  {
        
        $excel->sheet('Hoja 1', function($sheet) use($fields,$centroContable,$date){
            
            $UserEntity = User::find(auth()->id());
            
            $adendas = $fields;
            $sheet->setAutoSize(true);
            $sheet->mergeCells('A1:F1');
            $sheet->setColumnFormat(array(
                'D10:D10000' => '"$"#,##0.00',
            ));
            $sheet->cell('A1', function($cell) {
                $cell->setValue('DETALLE DE COMPROMETIDO');
                $cell->setFontWeight('bold');
                $cell->setAlignment('center');
                $cell->setFontSize(14);
            });
            $sheet->cell('B3', function($cell) {
                $cell->setValue('Empresa:');
                $cell->setAlignment('center');
            });
            $sheet->cell('B4', function($cell) {
                $cell->setValue('Proyecto:');
                $cell->setAlignment('center');
            });
            $sheet->cell('B5', function($cell) {
                $cell->setValue('Generado por:');
                $cell->setAlignment('center');
            });
            $sheet->cell('B6', function($cell) {
                $cell->setValue('Fecha de generación:');
                $cell->setAlignment('center');
            });
            $sheet->cell('C3', function($cell) {
                $cell->setValue('Ingeniería R-M, S.A.');
            });
            $sheet->cell('C4', function($cell) use($centroContable) {
                $cell->setValue($centroContable->nombre_centro);
                $cell->setAlignment('left');
            });
            $sheet->cell('C5', function($cell) use($UserEntity) {
                $cell->setValue($UserEntity->name);
                $cell->setAlignment('left');
            });
            $sheet->cell('C6', function($cell) use($date) {
                $cell->setValue($date);
                $cell->setAlignment('left');
            });
            $sheet->setHeight(8, 30);
            $sheet->setBorder('A8:E8', 'thin');
            $sheet->cells('A8:E8', function($cells) {
                $cells->setAlignment('center');
                $cells->setValignment('center');
                $cells->setBackground('#BDD7EE');
            });
            $sheet->cell('D8', function($cell) {
                $cell->setAlignment('left');
            });
            $sheet->cells('B10:B10000', function($cells) {
                $cells->setAlignment('center');
            });
            $sheet->cells('C8:C10000', function($cells) {
                $cells->setAlignment('left');
            });
            $sheet->cells('D10:D10000', function($cells) {
                $cells->setAlignment('right');
            });
            $sheet->cells('E10:E10000', function($cells) {
                $cells->setAlignment('center');
            });
            $sheet->cells('F8:F10000', function($cells) {
                $cells->setAlignment('center');
            });
            $sheet->fromArray($adendas,null,'A8');
            
        });
    })->export('xls');
    
}
public function exportDetalleGastado($centro, $idCuenta, $categoriaFilter, $dateFilterDesde, $dateFilterHasta, $montoFilterDesde, $montoFilterHasta, $proveedorFilter,Request $request){ // exportar en vista de gastado el detalle de gastado
    
    $dateFilterDesde = '';
    if($request->has('desde'))
    $dateFilterDesde = $request->desde;
    $dateFilterHasta = '';
    if($request->has('hasta'))
    $dateFilterHasta = $request->hasta;
    if($dateFilterDesde=='')
    $dateFilterDesde='12/12/0000';
    if($dateFilterHasta=='')
    $dateFilterHasta='01/01/2100';
    
    $date = Carbon::now()->format('d/m/Y');
    
    $idCentro = isset($centro)?$centro: '1';
    if($idCuenta=='null')
    $idCuenta = [''];
    else
    $idCuenta = explode(',',$idCuenta);
    if($categoriaFilter=='null')
    $categoriaFilter = [''];
    else
    $categoriaFilter = array_filter(explode(',',$categoriaFilter));
    
    $centroContable = CentroContable::find($idCentro);
    
    $fields = null;
    
    $total = 0;
    
    $keyOrder = "cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 3 ),'.',-1) as unsigned),
    cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 4 ),'.',-1) as unsigned),
    cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 5 ),'.',-1) as unsigned)";
    
    $cuentas_fact = DB::table('cuentas')
    ->select('cuentas.id_cuenta','cuentas.nombre_cuenta','cuentas.categoria')
    ->join('facturas', 'cuentas.id_cuenta', '=', 'facturas.id_cuenta')
    ->where('facturas.id_centro',$idCentro)
    ->where(function($q) use ($idCuenta){
        foreach($idCuenta as $value){
            $q->orWhere('cuentas.id_cuenta', 'LIKE', $value.'%');
        }
    })
    ->where(function($q) use ($categoriaFilter){
        foreach($categoriaFilter as $value){
            $q->orWhere('cuentas.categoria', 'LIKE', '%'.$value.'%');
        }
    });
    
    $cuentas = DB::table('cuentas')
    ->select('cuentas.id_cuenta','cuentas.nombre_cuenta','cuentas.categoria')
    ->join('planilla', 'cuentas.id_cuenta', '=', 'planilla.id_cuenta')
    ->where('planilla.id_centro',$idCentro)
    ->where(function($q) use ($idCuenta){
        foreach($idCuenta as $value){
            $q->orWhere('cuentas.id_cuenta', 'LIKE', $value.'%');
        }
    })
    ->where(function($q) use ($categoriaFilter){
        foreach($categoriaFilter as $value){
            $q->orWhere('cuentas.categoria', 'LIKE', '%'.$value.'%');
        }
    })
    ->union($cuentas_fact)->orderBy(\DB::raw($keyOrder))->distinct('cuentas.id_cuenta')->get();
    
    
    foreach ($cuentas as $cuenta){ 
        
        $gastado = Factura::where('id_cuenta', $cuenta->id_cuenta)->where('id_centro',$idCentro)
        ->whereDate('fecha_transaccion','>=', Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d'))
        ->whereDate('fecha_transaccion','<=', Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d'))
        ->get();
        $planilla = Planilla::where('id_cuenta', $cuenta->id_cuenta)->where('id_centro',$idCentro)
        ->whereDate('fecha_transaccion','>=', Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d'))
        ->whereDate('fecha_transaccion','<=', Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d'))
        ->get();  
        
        $totalcuenta=0;
        
        $first=true;
        $firstTotalCuenta=false;
        
        foreach ($gastado as $gasto){
            $add=true;
            if($proveedorFilter != 'null'){
                if(stripos($gasto->nombre_proveedor,$proveedorFilter)===false)
                $add = false;
            }
            if($dateFilterDesde!= 'null'){
                $date_init = strtotime($dateFilterDesde);
                $ua_comp = strtotime(isset($gasto->fecha_transaccion)? $gasto->fecha_transaccion:'1999-01-01 00:00:00');
                if($ua_comp < $date_init)
                $add = false;
            } 
            if($dateFilterHasta!= 'null'){
                $date_init = strtotime($dateFilterHasta);
                $ua_comp = strtotime(isset($gasto->fecha_transaccion)? $gasto->fecha_transaccion:'1999-01-01 00:00:00');
                if($ua_comp > $date_init)
                $add = false;
            }
            if($montoFilterDesde!= 'null'){
                if($montoFilterDesde > $gasto->monto)
                $add = false;
            }
            if($montoFilterHasta!= 'null'){
                if($montoFilterHasta < $gasto->monto)
                $add = false;
            }
            if($add){
                if($first){
                    $resp['id_cuenta']=$cuenta->id_cuenta;
                    $resp['Categoría'] = $cuenta->id_cuenta.' '.$cuenta->nombre_cuenta;
                    $resp['Fecha'] = '';
                    $resp['Descripción'] = '';
                    $resp['Monto'] = '';
                    $resp['Factura'] = '';
                    $resp['Proveedor'] = '';
                    
                    $fields[] = $resp;
                    $first=false;
                    $firstTotalCuenta=true;
                }
                $fields[] = array('Categoría' => $cuenta->categoria,'Fecha' => Carbon::parse($gasto->fecha_transaccion)->format('d/m/Y'), 'Descripción' => $gasto->desc_transaccion,'Monto' => floatval($gasto->monto),'Factura' => $gasto->num_fact,'Proveedor' => $gasto->nombre_proveedor);
                $total+=$gasto->monto;
                $totalcuenta+=$gasto->monto;
            }
        }
        foreach ($planilla as $plan){ 
            $add=true;
            if($proveedorFilter != 'null'){
                if(stripos($plan->nombre_proveedor,$proveedorFilter)===false)
                $add = false;
            }
            if($dateFilterDesde!= 'null'){
                $date_init = strtotime($dateFilterDesde);
                $ua_comp = strtotime(isset($plan->fecha_transaccion)? $plan->fecha_transaccion:'1999-01-01 00:00:00');
                if($ua_comp < $date_init)
                $add = false;
            }
            if($dateFilterHasta!= 'null'){
                $date_init = strtotime($dateFilterHasta);
                $ua_comp = strtotime(isset($plan->fecha_transaccion)? $plan->fecha_transaccion:'1999-01-01 00:00:00');
                if($ua_comp > $date_init)
                $add = false;
            }
            if($montoFilterDesde!= 'null'){
                if($montoFilterDesde > $plan->monto)
                $add = false;
            }
            if($montoFilterHasta!= 'null'){
                if($montoFilterHasta < $plan->monto)
                $add = false;
            }
            if($add){
                if($first){
                    $resp['id_cuenta']=$cuenta->id_cuenta;
                    $resp['Categoría'] = $cuenta->id_cuenta.' '.$cuenta->nombre_cuenta;
                    $resp['Fecha'] = '';
                    $resp['Descripción'] = '';
                    $resp['Monto'] = '';
                    $resp['Factura'] = '';
                    $resp['Proveedor'] = '';
                    
                    $fields[] = $resp;
                    $first=false;
                    $firstTotalCuenta=true;
                }
                $fields[] = array('Categoría' => $cuenta->categoria,'Fecha' => Carbon::parse($plan->fecha_transaccion)->format('d/m/Y'), 'Descripción' => $plan->desc_transaccion,'Monto' => floatval($plan->monto),'Factura' => $plan->num_planilla,'Proveedor' => 'Recursos Humanos');
                $total+=$plan->monto;
                $totalcuenta+=$plan->monto;
            }
        }
        if($firstTotalCuenta){
            $fields[] = array('Categoría' => '','Fecha' => '','Descripción' => 'Total','Monto' => $totalcuenta,'Factura' => '','Proveedor' => '');
            $fields[] = array('Categoría' => '','Fecha' => '','Descripción' => '','Monto' => '','Factura' => '','Proveedor' => '');
            $firstTotalCuenta=false;
        }
    }
    $divisores = Cuenta::where('categoria','=','Divisor')->get()->sort(function($A, $B){
        return strnatcasecmp($A->id_cuenta, $B->id_cuenta);
    });
    
    $actual = $divisores->shift();
    $index=0;
    foreach($fields as $field)
    {
        $id_cuenta = array_key_exists('id_cuenta',$field)>0?$field['id_cuenta']:'';
        
        if($id_cuenta== "" || !array_key_exists('id_cuenta',$field)){
            $index++;
            continue;
        }
            
        if($actual && strnatcasecmp($actual->id_cuenta,$id_cuenta)<=0){
            $act = explode(".",$actual->id_cuenta);
            $cue = explode(".",$id_cuenta);
            if($act[3][0]==$cue[3][0]){
                $div= array(array(
                    'id_cuenta'=>$actual->id_cuenta,
                    'Categoría'=>$actual->nombre_cuenta,
                    'Fecha'=>$actual->categoria,
                    'Descripcion'=>$actual->id_cuenta.' '.$actual->nombre_cuenta,
                    'Monto'=>'PUEBASBAS',
                    'Factura'=>'',
                    'Proveedor'=>''
                ));
                array_splice($fields,$index, 0 , $div);
                $index++;
            }
            while($actual && $actual && strnatcasecmp($actual->id_cuenta,$id_cuenta)<=0){
                $actual = $divisores->shift();
            }
        }
        $index++;
    }    
    
    $fields[] = array('Categoría' => '','Fecha' => '','Descripción' => 'Total general:' ,'Monto' => $total,'Factura' => '','Proveedor' => '');
    
    Excel::create('detalle_costos', function($excel) use($fields,$centroContable,$date,  $dateFilterDesde, $dateFilterHasta)  {
        
        $excel->sheet('Hoja 1', function($sheet) use($fields,$centroContable,$date ,  $dateFilterDesde, $dateFilterHasta){
            
            $UserEntity = User::find(auth()->id());
            
            $adendas = $fields;
            $sheet->setAutoSize(true);
            $sheet->mergeCells('A1:F1');
            $sheet->setColumnFormat(array(
                'D10:D10000' => '"$"#,##0.00',
            ));
            $sheet->cell('A1', function($cell) {
                $cell->setValue('DETALLE DE COSTOS');
                $cell->setFontWeight('bold');
                $cell->setAlignment('center');
                $cell->setFontSize(14);
            });
            $sheet->cell('B3', function($cell) {
                $cell->setValue('Empresa:');
                $cell->setAlignment('center');
            });
            $sheet->cell('B4', function($cell) {
                $cell->setValue('Proyecto:');
                $cell->setAlignment('center');
            });
            $sheet->cell('B5', function($cell) {
                $cell->setValue('Generado por:');
                $cell->setAlignment('center');
            });
            $sheet->cell('B6', function($cell) {
                $cell->setValue('Fecha de generación:');
                $cell->setAlignment('center');
            });
            $sheet->cell('B7', function($cell) {
                $cell->setValue('Rango:');
                $cell->setAlignment('center');
            });
            $sheet->cell('C3', function($cell) {
                $cell->setValue('Ingeniería R-M, S.A.');
            });
            $sheet->cell('C4', function($cell) use($centroContable) {
                $cell->setValue($centroContable->nombre_centro);
                $cell->setAlignment('left');
            });
            $sheet->cell('C5', function($cell) use($UserEntity) {
                $cell->setValue($UserEntity->name);
                $cell->setAlignment('left');
            });
            $sheet->cell('C6', function($cell) use($date) {
                $cell->setValue($date);
                $cell->setAlignment('left');
            });
            $sheet->cell('C7', function($cell) use ( $dateFilterDesde, $dateFilterHasta) {
                $cell->setValue('Desde: '. $dateFilterDesde ." Hasta: ". $dateFilterHasta);
                $cell->setAlignment('center');
            });
            $sheet->setHeight(8, 30);
            $sheet->setBorder('A8:F8', 'thin');
            $sheet->cells('A8:F8', function($cells) {
                $cells->setAlignment('center');
                $cells->setValignment('center');
                $cells->setBackground('#BDD7EE');
            });
            $sheet->cell('D8', function($cell) {
                $cell->setAlignment('left');
            });
            $sheet->cells('B10:B10000', function($cells) {
                $cells->setAlignment('center');
            });
            $sheet->cells('C8:C10000', function($cells) {
                $cells->setAlignment('left');
            });
            $sheet->cells('D10:D10000', function($cells) {
                $cells->setAlignment('right');
            });
            $sheet->cells('E10:E10000', function($cells) {
                $cells->setAlignment('center');
            });
            $sheet->cells('F8:F10000', function($cells) {
                $cells->setAlignment('center');
            });
            foreach($adendas as$key => $row){
                if(array_key_exists('id_cuenta',$row)&&array_key_exists('Fecha',$row)&& $row['Fecha']=="Divisor"){
                    $adendas[$key]['Fecha']="";
                    $sheet->row($key+9, function($row) {
                        $row->setBackground('#E5E5E5');
                    });
                }
            }
            
            $sheet->fromArray($adendas,null,'A8');
            
        });
    })->export('xls');
    
}
public function ExportDetailCorto($centro, Request $request) // exportar presupuesto de proyecto corto en excel (Exportar (Corto))
{   
    $dateFilterDesde = '';
    if($request->has('desde'))
    $dateFilterDesde = $request->desde;
    $dateFilterHasta = '';
    if($request->has('hasta'))
    $dateFilterHasta = $request->hasta;
    if($dateFilterDesde=='')
    $dateFilterDesde='12/12/0000';
    if($dateFilterHasta=='')
    $dateFilterHasta='01/01/2100';
    $table='presupuesto_y_avance';
    $total['Presupuesto']=$total['Adendas']=$total['Presupuesto Total']=$total['Comprometido']=$total['Gastado']=0;
    
    $idCentro = isset($centro)?$centro: '1';
    
    $centroContable = CentroContable::find($idCentro);
    
    $items = null;
    
    $query_categorias = "SELECT (cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 5 ),'.',-1) as signed)) as categoria, 
    SUM(presupuesto) as presupuesto FROM `presupuesto_y_avance` where id_centro_contable='".$idCentro."' 
    and (cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 5 ),'.',-1) as signed)) != '0' 
    GROUP BY (cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 5 ),'.',-1) as signed))";
    
    $query = DB::select($query_categorias);
    foreach ($query as $cuenta){
        $nombres = ['MAT','ACE','AST','CON','CEM','ARE','BLQ','PIE','MDO','PRE','EQP','OTR','SUB'];
        $comp = OrdenCompra::where('id_centro', $idCentro)
        ->whereRaw("(cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 5 ),'.',-1) as unsigned)) = ".$cuenta->categoria."")
        ->whereDate('fecha_transaccion','>=', Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d'))
        ->whereDate('fecha_transaccion','<=', Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d'))
        ->sum('monto_compra');
        $gastado = Factura::where('id_centro', $idCentro)
        ->whereRaw("(cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 5 ),'.',-1) as unsigned)) = ".$cuenta->categoria."")
        ->whereDate('fecha_transaccion','>=', Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d'))
        ->whereDate('fecha_transaccion','<=', Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d'))
        ->sum('monto');
        $planilla = Planilla::where('id_centro', $idCentro)
        ->whereRaw("(cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 5 ),'.',-1) as unsigned)) = ".$cuenta->categoria."")
        ->whereDate('fecha_transaccion','>=', Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d'))
        ->whereDate('fecha_transaccion','<=', Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d'))
        ->sum('monto');
        
        $querya = DB::table('items_detalle')
        ->select(\DB::raw("monto_nuevo, monto_anterior"))
        ->join('items', 'items.id', '=','items_detalle.id_item')
        ->join('ajustes', 'ajustes.id', '=','items.id_ajuste')
        ->join('adendas', 'adendas.id', '=','ajustes.id_adenda')
        ->where('adendas.id_centro','=',$idCentro)
        ->where('adendas.estado','=','aprobado')
        ->whereRaw("(cast(SUBSTRING_INDEX(SUBSTRING_INDEX(items_detalle.id_cuenta, '.', 5 ),'.',-1) as unsigned)) = ".$cuenta->categoria."");
        
        $adendas=$querya->sum('monto_nuevo')/*-$querya->sum('monto_anterior')*/;
        
        $resp['Categoria']=$cuenta->categoria;
        $resp['Abreviatura']=$nombres[$cuenta->categoria-1];
        $resp['Presupuesto']=$cuenta->presupuesto;
        $resp['Adendas']=$adendas;
        $resp['Presupuesto Total']=$cuenta->presupuesto+$adendas;
        $resp['Comprometido']=$comp;
        $resp['Gastado']=$gastado+$planilla;
        
        $total['Presupuesto'] += $resp['Presupuesto'];
        $total['Adendas'] += $resp['Adendas'];
        $total['Presupuesto Total'] += $resp['Presupuesto Total'];                            
        $total['Comprometido'] += $resp['Comprometido'];
        $total['Gastado'] += $resp['Gastado'];
        
        $items[] = $resp;
        
    }
    
    $items[] = array('','TOTALES',$total['Presupuesto'],$total['Adendas'],$total['Presupuesto Total'],$total['Comprometido'],$total['Gastado']);
    
    Excel::create('actualizado_costos_corto', function($excel) use($items,$centroContable,$request)  {
        
        $excel->sheet('Hoja 1', function($sheet) use($items,$centroContable,$request){
            
            $date = Carbon::now()->format('d/m/Y');
            $UserEntity = User::find(auth()->id());
            
            $adendas = $items;
            $sheet->setAutoSize(true);
            $sheet->mergeCells('A1:D1');
            $sheet->mergeCells('F1:G1');
            $sheet->mergeCells('B2:C2');
            $sheet->mergeCells('B3:C3');
            $sheet->mergeCells('F2:G2');
            $sheet->mergeCells('F3:G3');
            $sheet->setColumnFormat(array(
                'C5:C10000' => '"$"#,##0.00_-',
                'D5:D1000' => '"$"#,##0.00_-',
                'E5:E1000' => '"$"#,##0.00_-',
                'F5:F10000' => '"$"#,##0.00_-',
                'G5:G10000' => '"$"#,##0.00_-',
            ));
            $sheet->cell('E1', function($cell) {
                $cell->setValue('Rango de fechas:');
            });
            $sheet->cell('F1', function($cell) use($request) {
                $cell->setValue('De: '.$request->desde.' Hasta: '.$request->hasta);
                $cell->setAlignment('center');
            });
            $sheet->cell('A1', function($cell) {
                $cell->setValue('Actualizado de costos (Corto)');
                $cell->setFontWeight('bold');
                $cell->setAlignment('center');
                $cell->setFontSize(14);
            });
            $sheet->cell('B2', function($cell) {
                $cell->setValue('Ingeniería RM');
                $cell->setAlignment('center');
            });
            $sheet->cell('B3', function($cell) use($centroContable) {
                $cell->setValue($centroContable->nombre_centro);
                $cell->setAlignment('center');
            });
            $sheet->cell('A2', function($cell) {
                $cell->setValue('Empresa:');
            });
            $sheet->cell('A3', function($cell) {
                $cell->setValue('Proyecto:');
            });
            $sheet->cell('E2', function($cell) {
                $cell->setValue('Fecha del reporte:');
            });
            $sheet->cell('E3', function($cell) {
                $cell->setValue('Generado por:');
            });
            $sheet->cell('F2', function($cell) use($date) {
                $cell->setValue($date);
                $cell->setAlignment('center');
            });
            $sheet->cell('F3', function($cell) use($UserEntity) {
                $cell->setValue($UserEntity->name);
                $cell->setAlignment('center');
            });
            $sheet->setHeight(4, 30);
            $sheet->setBorder('A4:G4', 'thin');
            $sheet->cells('A4:G4', function($cells) {
                $cells->setAlignment('center');
                $cells->setValignment('center');
                $cells->setBackground('#BDD7EE');
            });
            $sheet->cells('A5:B10000', function($cells) {
                $cells->setAlignment('center');
            });
            $sheet->cells('C5:G10000', function($cells) {
                $cells->setAlignment('right');
            });
            $sheet->fromArray($adendas,null,'A4');
            
        });
    })->export('xls');
}
public function exportHome (){ // exportar Home (Exportar)
    $items = null;
    
    $centros = DB::table('centros_contables')
    ->join('centro_contable_user', 'centros_contables.id_centro', '=', 'centro_contable_user.centro_contable_id')
    ->where('centros_contables.totalizador',0)
    ->where('centro_contable_user.user_id',auth()->id())
    ->orderBy('centros_contables.id_padre', 'asc')
    ->orderBy('centros_contables.id_centro', 'asc')->get();
    
    foreach ($centros as $centro){
        $ultimaActualizacion = DB::table('log_cambios')->where("id_centro",$centro->id_centro)->max('fecha_entrada');
        
        $adendas = app('App\Http\Controllers\AdendaController')->getMontoAdendasByProyecto($centro->id_centro);
        $presupuesto = DB::table('presupuesto_y_avance')->where("id_centro_contable",$centro->id_centro)->sum('presupuesto');
        $gastado = Factura::where('id_centro', $centro->id_centro)->sum('monto');
        $planilla = Planilla::where('id_centro', $centro->id_centro)->sum('monto');
        
        $resp['Centro Contable']=$centro->nombre_centro;
        $resp['PM']=$centro->contratante;
        $resp['Tel. del PM']=$centro->tel_contratante;
        $resp['Tipo']=$centro->tipo;
        $resp['Presupuesto Original'] = $presupuesto;
        $resp['Adendas']=$adendas;
        $resp['Presupuesto Total']= $presupuesto+$adendas;
        $resp['Gastado']=$gastado+$planilla;
        $resp['Ultima Actualizacion']= $ultimaActualizacion;
        
        $items[] = $resp;
    }
    
    Excel::create('proyectos', function($excel) use($items)  {
        
        $excel->sheet('Hoja 1', function($sheet) use($items){
            
            $date = Carbon::now()->format('d/m/Y');
            $UserEntity = User::find(auth()->id());
            
            $adendas = $items;
            $sheet->setAutoSize(true);
            $sheet->mergeCells('A1:I1');
            $sheet->mergeCells('B2:D2');
            $sheet->mergeCells('B3:D3');
            $sheet->mergeCells('G2:I2');
            $sheet->mergeCells('G3:I3');
            $sheet->setColumnFormat(array(
                'E5:E10000' => '"$"#,##0.00_-',
                'F5:F10000' => '"$"#,##0.00_-',
                'G5:G10000' => '"$"#,##0.00_-',
                'H5:H10000' => '"$"#,##0.00_-',
            ));
            $sheet->cell('A1', function($cell) {
                $cell->setValue('Proyectos');
                $cell->setFontWeight('bold');
                $cell->setAlignment('center');
                $cell->setFontSize(14);
            });
            $sheet->cell('B2', function($cell) {
                $cell->setValue('Ingeniería RM');
                $cell->setAlignment('center');
            });
            $sheet->cell('B3', function($cell) {
                $cell->setValue('Todos');
                $cell->setAlignment('center');
            });
            $sheet->cell('A2', function($cell) {
                $cell->setValue('Empresa:');
            });
            $sheet->cell('A3', function($cell) {
                $cell->setValue('Proyecto:');
            });
            $sheet->cell('F2', function($cell) {
                $cell->setValue('Fecha del reporte:');
            });
            $sheet->cell('F3', function($cell) {
                $cell->setValue('Generado por:');
            });
            $sheet->cell('G2', function($cell) use($date) {
                $cell->setValue($date);
                $cell->setAlignment('center');
            });
            $sheet->cell('G3', function($cell) use($UserEntity) {
                $cell->setValue($UserEntity->name);
                $cell->setAlignment('center');
            });
            $sheet->setHeight(4, 30);
            $sheet->setBorder('A4:I4', 'thin');
            $sheet->cells('A4:I4', function($cells) {
                $cells->setAlignment('center');
                $cells->setValignment('center');
                $cells->setBackground('#BDD7EE');
            });
            
            $sheet->cells('E5:H10000', function($cells) {
                $cells->setAlignment('right');
            });
            
            $sheet->fromArray($adendas,null,'A4');
            
        });
    })->export('xls');          
}
public function exportViewDetail(Request $request) // exportar vista actual presupuesto de proyecto en excel (Exportar vista actual)
{   
    $content = $request->all();
    $idCentro = isset($content['idCentro'])?$content['idCentro']: '';
    $rowsData = json_decode($content['rows'], true);
    $centroContable = CentroContable::find($idCentro);
    $items = ($rowsData);
    $excel = Excel::create('actualizado_costos_vista', function($excel) use($items,$centroContable)  {
        
        $excel->sheet('Hoja 1', function($sheet) use($items,$centroContable){
            
            $date = Carbon::now()->format('d/m/Y');
            $UserEntity = User::find(auth()->id());
            $adendas = $items;
            $sheet->setAutoSize(true);
            $sheet->mergeCells('A1:L1');
            $sheet->mergeCells('B2:D2');
            $sheet->mergeCells('B3:D3');
            $sheet->mergeCells('H2:J2');
            $sheet->mergeCells('H3:J3');
            $sheet->setColumnFormat(array(
                'C5:C10000' => '"$"#,##0.00_-',
                'D5:D10000' => '"$"#,##0.00_-',
                'E5:E10000' => '"$"#,##0.00_-',
                'G5:L10000' => '"$"#,##0.00_-',
                'F5:F10000' => '0.00"%"',
            ));
            $sheet->cell('A1', function($cell) {
                $cell->setValue('Actualizado de costos (Vista Actual)');
                $cell->setFontWeight('bold');
                $cell->setAlignment('center');
                $cell->setFontSize(14);
            });
            $sheet->cell('B2', function($cell) {
                $cell->setValue('Ingeniería RM');
                $cell->setAlignment('center');
            });
            $sheet->cell('B3', function($cell) use($centroContable) {
                $cell->setValue($centroContable->nombre_centro);
                $cell->setAlignment('center');
            });
            $sheet->cell('A2', function($cell) {
                $cell->setValue('Empresa:');
            });
            $sheet->cell('A3', function($cell) {
                $cell->setValue('Proyecto:');
            });
            $sheet->cell('G2', function($cell) {
                $cell->setValue('Fecha del reporte:');
            });
            $sheet->cell('G3', function($cell) {
                $cell->setValue('Generado por:');
            });
            $sheet->cell('H2', function($cell) use($date) {
                $cell->setValue($date);
                $cell->setAlignment('center');
            });
            $sheet->cell('H3', function($cell) use($UserEntity) {
                $cell->setValue($UserEntity->name);
                $cell->setAlignment('center');
            });
            $sheet->setHeight(4, 30);
            $sheet->setBorder('A4:M4', 'thin');
            $sheet->cells('A4:M4', function($cells) {
                $cells->setAlignment('center');
                $cells->setValignment('center');
                $cells->setBackground('#BDD7EE');
            });
            $sheet->cells('C5:E10000', function($cells) {
                $cells->setAlignment('right');
            });
            $sheet->cells('F5:F10000', function($cells) {
                $cells->setAlignment('center');
            });
            $sheet->cells('G5:L10000', function($cells) {
                $cells->setAlignment('right');
            });
            $arr = (object)array_map(function($adendas) { return is_array($adendas) ? (object)$adendas :  $adendas;  }, $adendas);
            $it=5;
            $new_value="";
            foreach ($arr as $key_value) {
                if(substr($key_value->id, -3)=="00."){
                    $sheet->row($it, function($row) {
                        $row->setBackground('#E5E5E5');
                    });
                }
                if($key_value!==$new_value){
                $it++;
                }
                $new_value=$key_value->id;

            }           
            $sheet->fromArray($adendas,null,'A4');
            
        });
    })->store('xls',storage_path('../public/documents/files'),true);
    
    return '/documents/files/actualizado_costos_vista.xls';
}
public function exportHomeAdenda(Request $request) // exportar vista actual presupuesto de proyecto en excel (Exportar vista actual)
{   
    $content = $request->all();
    $test=$request->centro;
    $textFilter = isset($content['centro'])?$content['centro']: '';
    $estadoFilter = isset($content['estado'])?$content['estado']: '';
    $total['Costo']=$total['Utilidad']=$total['Admin.']=$total['Subtotal']=$total['ITBMS']=$total['Total']=0;
    $bproyecto=$request->centro;
    $bestado=$request->estado;
    $bfechai=$request->fechai;
    $bfechaf=$request->fechaf;
    $bnumero=$request->numero;
    $filtro=$request->filtro;
    switch ($filtro) {
        case '0':
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->orderBy('adendas.fecha', 'desc')->get();        
            break;
        case '1':
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('adendas.numero','=',$bnumero)
            ->orderBy('adendas.fecha', 'desc')->get();             
            break;
        case '2':
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('adendas.numero','=',$bnumero)
            ->whereBetween('adendas.fecha',[$bfechai , $bfechaf])
            ->orderBy('adendas.fecha', 'desc')->get();        
            break;
        case '3':
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('adendas.estado','=',$bestado)
            ->where('adendas.numero','=',$bnumero)
            ->whereBetween('adendas.fecha',[$bfechai , $bfechaf])
            ->orderBy('adendas.fecha', 'desc')->get();              
            break;
        case '4':
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('centros_contables.nombre_centro','LIKE','%'.$request->centro.'%')
            ->where('adendas.estado','=',$bestado)
            ->where('adendas.numero','=',$bnumero)
            ->whereBetween('adendas.fecha',[$bfechai , $bfechaf])
            ->orderBy('adendas.fecha', 'desc')->get();                          
            break;
        case '5':
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('centros_contables.nombre_centro','LIKE','%'.$request->centro.'%')
            ->orderBy('adendas.fecha', 'desc')->get();                          
            break;
        case '6':
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('centros_contables.nombre_centro','LIKE','%'.$request->centro.'%')
            ->where('adendas.estado','=',$bestado)
            ->orderBy('adendas.fecha', 'desc')->get();            
            break;
        case '7':
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('centros_contables.nombre_centro','LIKE','%'.$request->centro.'%')
            ->where('adendas.estado','=',$bestado)
            ->orderBy('adendas.fecha', 'desc')->get();             
            break;
        case '8':
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('adendas.estado','=',$bestado)
            ->orderBy('adendas.fecha', 'desc')->get();                          
            break;        
        case '9':
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('adendas.numero','=',$bnumero)
            ->where('adendas.estado','=',$bestado)
            ->orderBy('adendas.fecha', 'desc')->get();                          
            break;        
        case '10':
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->whereBetween('adendas.fecha',[$bfechai , $bfechaf])
            ->orderBy('adendas.fecha', 'desc')->get();                          
            break; 
        default:
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->orderBy('adendas.fecha', 'desc')->get(); 
            break;
    }
    /*if(isset($textFilter) && $textFilter!= ''){
        $adendas = DB::table('adendas')
        ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
        ->where('centros_contables.nombre_centro','LIKE','%'.$request->centro.'%')
        ->orderBy('adendas.fecha', 'desc')->get();
    }
    else
    $adendas = Adenda::orderBy('fecha', 'desc')->get();*/
    
    $items = null;
    
    foreach ($adendas as $adenda){
        $addTrue = true;
        if(isset($estadoFilter) && $estadoFilter!= '' && $estadoFilter!= 'todos'){
            if(strrpos($adenda->estado,$estadoFilter)===false)
            $addTrue = false;
        }
        if($addTrue){
            $centroEntity = CentroContable::find($adenda->id_centro);
            $resp['Proyecto']=$centroEntity->nombre_centro;
            $resp['No. de documento']=$adenda->numero;
            $resp['Fecha de doc.']=Carbon::parse($adenda->fecha)->format('d/m/Y');
            $resp['Descripción']=trim($adenda->descripcion);
            $resp['Costo']= floatval(app('App\Http\Controllers\AdendaController')->getCostoByAdenda($adenda->id));
            $resp['Utilidad'] = floatval(app('App\Http\Controllers\AdendaController')->getUtilidadByAdenda($adenda->id));
            $resp['Admin.']= floatval(app('App\Http\Controllers\AdendaController')->getAdministracionByAdenda($adenda->id));
            $resp['Subtotal']= $resp['Costo'] + $resp['Utilidad'] + $resp['Admin.'];
            $resp['ITBMS']= floatval(app('App\Http\Controllers\AdendaController')->getItbmsByAdenda($adenda->id));
            $resp['Total']= $resp['Subtotal'] + $resp['ITBMS'];
            $resp['Estado']=app('App\Http\Controllers\AdendaController')->getEstadobyShort($adenda->estado);
            $items[] = $resp;
            
            $total['Costo'] += $resp['Costo'];
            $total['Utilidad'] += $resp['Utilidad'];
            $total['Admin.'] += $resp['Admin.'];
            $total['Subtotal'] += $resp['Subtotal'];                              
            $total['ITBMS'] += $resp['ITBMS'];
            $total['Total'] += $resp['Total'];
            
            $ajustes = Ajuste::where('id_adenda',$adenda->id)->get();
            
            foreach ($ajustes as $ajuste){   
                
                $resp['Proyecto']='';
                $resp['No. de documento']=$ajuste->numero;
                $resp['Fecha de doc.']=Carbon::parse($ajuste->fecha)->format('d/m/Y');
                $resp['Descripción']=trim($ajuste->descripcion);
                $resp['Costo']= floatval(app('App\Http\Controllers\AdendaController')->getCostoByAjuste($ajuste->id));
                $resp['Utilidad'] = floatval($ajuste->utilidad);
                $resp['Admin.']= floatval($ajuste->administracion);
                $resp['Subtotal']= $resp['Costo'] + $resp['Utilidad'] + $resp['Admin.'];
                $resp['ITBMS']= floatval($ajuste->itbms);
                $resp['Total']= $resp['Subtotal'] + $resp['ITBMS'];
                $resp['Estado']='';
                
                $items[] = $resp;
            }
            $items[] = array('Proyecto' => '','No. de documento' => '','Fecha de doc.' => '','Descripción' => '','Costo' => '','Utilidad' => '','Admin.' => '','Subtotal' => '','ITBMS' => '','Total' => '','Estado' => '');
        }
    }
    $items[] = array('','','','TOTALES',$total['Costo'],$total['Utilidad'],$total['Admin.'],$total['Subtotal'],$total['ITBMS'],$total['Total'],'');
    
    $excel = Excel::create('Listado_Ajustes', function($excel) use($textFilter,$estadoFilter, $items,$bproyecto,$bnumero,$bestado,$bfechaf,$bfechai)  {
        
        $excel->sheet('Hoja 1', function($sheet) use($textFilter,$estadoFilter, $items,$bproyecto,$bnumero,$bestado,$bfechaf,$bfechai){
            
            $date = Carbon::now()->format('d/m/Y');
            $UserEntity = User::find(auth()->id());
            
            $sheet->setAutoSize(true);
            $sheet->mergeCells('A1:K1');
            $sheet->mergeCells('B3:C3');
            $sheet->mergeCells('B4:C4');
            $sheet->mergeCells('B5:C5');
            // $sheet->mergeCells('E3:J3');
            // $sheet->mergeCells('E4:J4');
            
            $sheet->setColumnFormat(array(
                'E7:J10000' => '"$"#,##0.00_-',
            ));
            $sheet->cell('A1', function($cell) {
                $cell->setValue('Listado de Ajustes');
                $cell->setFontWeight('bold');
                $cell->setAlignment('center');
                $cell->setFontSize(14);
            });
            $sheet->cell('A3', function($cell) {
                $cell->setValue('Empresa:');
            });   
            $sheet->cell('A4', function($cell) {
                $cell->setValue('Fecha del reporte:');
            });
            $sheet->cell('A5', function($cell) {
                $cell->setValue('Generado por:');
            });
            $sheet->cell('B3', function($cell) {
                $cell->setValue('Ingeniería RM');
                $cell->setAlignment('left');
            });
            $sheet->cell('B4', function($cell) use($date) {
                $cell->setValue($date);
                $cell->setAlignment('left');
            });
            $sheet->cell('B5', function($cell) use($UserEntity) {
                $cell->setValue($UserEntity->name);
                $cell->setAlignment('left');
            });
            $sheet->cell('D3', function($cell) {
                $cell->setValue('Centro:');
                $cell->setAlignment('left');
            });
            $sheet->cell('D4', function($cell) {
                $cell->setValue('Estado de la adenda:');
                $cell->setAlignment('left');
            });         
            $sheet->cell('E3', function($cell) use($textFilter) {       
                $cell->setValue($textFilter);
                if($textFilter=='')
                    $cell->setValue('Todos');
                $cell->setAlignment('left');
            });
            
            $sheet->cell('E4', function($cell) use($estadoFilter) {
                $cell->setValue(app('App\Http\Controllers\AdendaController')->getEstadobyShort($estadoFilter));
                $cell->setAlignment('left');
            });
            $sheet->setHeight(6, 30);
            $sheet->setBorder('A6:K6', 'thin');
            $sheet->cells('A6:K6', function($cells) {
                $cells->setAlignment('center');
                $cells->setValignment('center');
                //$cells->setBackground('#BDD7EE');
            });
            $sheet->cells('C7:D10000', function($cells) {
                $cells->setAlignment('center');
            });
            $sheet->cells('E7:J10000', function($cells) {
                $cells->setAlignment('right');
            });
            $sheet->fromArray($items,null,'A6');

            $sheet->setWidth('A', 30);
            $sheet->setWidth('B', 30);
            $sheet->setWidth('C', 20);
            $sheet->setWidth('D', 50);
            $sheet->setWidth('E', 20);
            $sheet->setWidth('F', 20);
            $sheet->setWidth('G', 20);
            $sheet->setWidth('H', 20);
            $sheet->setWidth('I', 20);
            $sheet->setWidth('J', 20);
            $sheet->setWidth('K', 10);
            $sheet->getStyle('D6:D'. $sheet->getHighestRow())
            ->getAlignment()->setWrapText(true); 
            
        });
    })->export('xls');;
}

public function ExportDetailDivision (Request $request)
{
    //DB::raw('SUM(
        $comp = OrdenCompra::where('id_centro',$request->centro)
        ->groupBy('ordenes_de_compra.id_cuenta')
        ->join('cuentas as l2','ordenes_de_compra.id_cuenta','=','l2.id_cuenta')
        ->join('cuentas as l1','l2.id_padre','=','l1.id_cuenta')
        ->join('cuentas as l0','l1.id_padre','=','l0.id_cuenta')
        ->selectRaw('l2.categoria, l2.id_cuenta as nivel2, l1.id_cuenta as nivel1, l0.id_cuenta as nivel0, sum(monto_compra) as comp')
        ->get();
        //dd($comp->groupBy('categoria')["MAT"]->sum('comp'));
        //dd($comp->groupBy('nivel0')["1.3.18."]->sum('comp'));
        $gast = Factura::where('id_centro',$request->centro)
        ->groupBy('facturas.id_cuenta')
        ->join('cuentas as l2','facturas.id_cuenta','=','l2.id_cuenta')
        ->join('cuentas as l1','l2.id_padre','=','l1.id_cuenta')
        ->join('cuentas as l0','l1.id_padre','=','l0.id_cuenta')
        ->selectRaw('l2.categoria, l2.id_cuenta as nivel2, l1.id_cuenta as nivel1, l0.id_cuenta as nivel0, sum(monto) as gast')
        ->get();
        //dd($gast->groupBy('nivel0')["1.3.1."]->groupBy('categoria')["PRE"]->sum('gast'));
        $plan = Planilla::where('id_centro',$request->centro)
        ->groupBy('planilla.id_cuenta')
        ->join('cuentas as l2','planilla.id_cuenta','=','l2.id_cuenta')
        ->join('cuentas as l1','l2.id_padre','=','l1.id_cuenta')
        ->join('cuentas as l0','l1.id_padre','=','l0.id_cuenta')
        ->selectRaw('l2.categoria, l2.id_cuenta as nivel2, l1.id_cuenta as nivel1, l0.id_cuenta as nivel0, sum(monto) as plan')
        ->get();
        //        dd($planilla);
        $preavan = PresupuestoAvance::where('id_centro_contable',$request->centro)
        ->join('cuentas as l2','presupuesto_y_avance.id_cuenta','=','l2.id_cuenta')
        ->join('cuentas as l1','l2.id_padre','=','l1.id_cuenta')
        ->join('cuentas as l0','l1.id_padre','=','l0.id_cuenta')
        ->select('l2.categoria', 
        'l2.id_cuenta',
        'l1.id_cuenta as nivel1',
        'l0.id_cuenta as nivel0',
        'presupuesto_y_avance.porcentaje_avance',
        'presupuesto_y_avance.presupuesto')
        ->get();
        
        $aden = Adenda::where('id_centro',$request->centro)
        ->join('ajustes as a','a.id_adenda','=','adendas.id')
        ->join('items as i','i.id_ajuste','=','a.id')
        ->join('items_detalle as id','id.id_item','=','i.id')
        ->join('cuentas as l2','l2.id_cuenta','=','id.id_cuenta')
        ->join('cuentas as l1','l2.id_padre','=','l1.id_cuenta')
        ->join('cuentas as l0','l1.id_padre','=','l0.id_cuenta')
        ->select('l2.categoria',
        'l1.id_cuenta as nivel1',
        'l0.id_cuenta as nivel0',
        'id.monto_nuevo')
        ->get();
        
        //dd($adendas->groupBy('nivel0')->first()->groupBy('categoria')->first()->sum('monto_nuevo'));
        //dd($presupuestoavance->groupBy('categoria')["MDO"]);
        
        Excel::create('resumen_division', function($excel) use($request, $comp,$gast,$plan,$preavan, $aden)  {
            
            $excel->sheet('Hoja 1', function($sheet) use($request, $comp,$gast,$plan,$preavan, $aden){
                $sheet->setAutoSize(true);
                $sheet->mergeCells('A1:G1');
                
                //                $sheet->mergeCells('A2:B2');
                //                $sheet->mergeCells('A3:B3');
                //                $sheet->mergeCells('A4:B4');
                
                $sheet->mergeCells('D2:E2');
                $sheet->mergeCells('D3:E3');
                $sheet->mergeCells('D4:E4');
                
                
                //$sheet->mergeCells('A2:C2');
                /*$sheet->setColumnFormat(array(
                    'C10:C10000' => '"$"#,##0.00'
                ));*/
                $sheet->cells('A1', function($cells) {
                    $cells->setValue('Resumen de costos por división (Gerencial)');
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                    $cells->setFontSize(14);
                });
                
                $sheet->cells('A2', function($cells) {
                    $cells->setValue('Empresa:');
                });
                $sheet->cells('B2', function($cells) use($request) {
                    $cells->setValue(CentroContable::find($request->centro)->nombre_centro);
                });
                
                $sheet->cells('A3', function($cells) {
                    $cells->setValue('Proyecto:');
                });
                $sheet->cells('B3', function($cells) use($request) {
                    $cells->setValue(CentroContable::find($request->centro)->nombre_proyecto);
                });
                
                $sheet->cells('A4', function($cells) {
                    $cells->setValue('Encargado:');
                });
                $sheet->cells('B4', function($cells) use($request) {
                    $cells->setValue("");
                });
                
                $sheet->cells('D2',function($cells){
                    $cells->setValue("Generado por:");
                });
                
                $sheet->cells('F2',function($cells){
                    $cells->setValue(User::find(auth()->id())->name);
                });
                
                $sheet->cells('D3',function($cells){
                    $cells->setValue("Fecha del reporte:");
                });
                
                $sheet->cells('F3',function($cells){
                    $cells->setValue(Carbon::now()->format('d/m/Y'));
                });
                
                
                $sheet->cells('D4',function($cells){
                    $cells->setValue("Tipo de proyecto:");
                });
                
                $sheet->cells('F4',function($cells) use ($request){
                    $cells->setValue(CentroContable::find($request->centro)->tipoProyecto->nombre);
                });
                
                $sheet->cells('B5',function($cells){
                    $cells->setValue('Presupuesto');
                });
                $sheet->cells('C5',function($cells){
                    $cells->setValue('Adendas');
                });
                $sheet->cells('D5',function($cells){
                    $cells->setValue('Presupuesto Total');
                });
                $sheet->cells('E5',function($cells){
                    $cells->setValue('Comprometido');
                });
                $sheet->cells('F5',function($cells){
                    $cells->setValue('Gastado');
                });
                $sheet->cells('G5',function($cells){
                    $cells->setValue('Diferencia vs Presupueso');
                });
                $sheet->setBorder('A5:G5', 'thin');
                $sheet->cells('A5:G5', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#BDD7EE');
                });
                
                
                
                $cellindex = 6;
                foreach($preavan->groupBy('nivel0') as $key=>$cuentas){
                    $sheet->cells('A'.$cellindex.':G'.$cellindex, function($cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setBackground('#BDD7EE');
                    });
                    $sheet->cells('A'.$cellindex++, function($cells) use($key,$cuentas) {
                        $cells->setValue($key.' '.Cuenta::find($key)->nombre_cuenta);
                    });
                    
                    $compcuenta = isset($comp->groupBy('nivel0')[$key])? $comp->groupBy('nivel0')[$key] :null;
                    $gastcuenta = isset($gast->groupBy('nivel0')[$key])? $gast->groupBy('nivel0')[$key] :null;
                    $plancuenta = isset($plan->groupBy('nivel0')[$key])? $plan->groupBy('nivel0')[$key] :null;
                    $adencuenta = isset($aden->groupBy('nivel0')[$key])? $aden->groupBy('nivel0')[$key] :null;
                    
                    foreach($cuentas->groupBy('categoria') as $cat=>$categoria){
                        
                        
                        $sheet->cells('A'.$cellindex, function($cells) use($cat,$categoria) {
                            $cells->setValue($cat);
                            $cells->setAlignment('right');
                        }); 
                        
                        $sheet->cells('B'.$cellindex, function($cells) use($cat,$categoria) {
                            $cells->setValue($categoria->sum('presupuesto'));
                        }); 
                        
                        $adendas = 0;
                        if($adencuenta!=null){
                            $adendas = isset($adencuenta->groupBy('categoria')[$cat])? $adencuenta->groupBy('categoria')[$cat]->sum('monto_nuevo') : '0';
                        }
                        $sheet->cells('C'.$cellindex, function($cells) use($adendas) {
                            $cells->setValue($adendas);
                        }); 
                        
                        $sheet->cells('D'.$cellindex, function($cells) use($adendas,$categoria) {
                            $cells->setValue($categoria->sum('presupuesto')+$adendas);
                        }); 
                        //dd($compcuenta->groupBy('categoria'));
                        $comprometido = 0;
                        if($compcuenta!=null){
                            $comprometido = isset($compcuenta->groupBy('categoria')[$cat])? $compcuenta->groupBy('categoria')[$cat]->sum('comp') : '0';
                        }
                        $sheet->cells('E'.$cellindex, function($cells) use($comprometido) {
                            $cells->setValue($comprometido);
                        }); 
                        
                        
                        $planilla = 0;
                        if($plancuenta!=null){
                            $planilla = isset($plancuenta->groupBy('categoria')[$cat])? $plancuenta->groupBy('categoria')[$cat]->sum('plan') : '0';
                        }
                        $gastado = 0;
                        if($gastcuenta!=null){
                            $gastado = isset($gastcuenta->groupBy('categoria')[$cat])? $gastcuenta->groupBy('categoria')[$cat]->sum('gast') : '0';
                        }
                        $gastado+=$planilla;
                        $sheet->cells('F'.$cellindex, function($cells) use($gastado) {
                            $cells->setValue($gastado);
                        }); 
                        
                        $porcentaje = 0;
                        foreach($categoria->groupBy('nivel1') as $padre => $cuentan2){
                            $porcentaje+=$cuentan2->sum('porcentaje_avance')/$cuentan2->count() ;
                        }
                        $porcentaje = $porcentaje / $categoria->groupBy('nivel1')->count();
                        
                        $costoproyectado = $porcentaje>0? ($gastado/$porcentaje)*100 : $categoria->sum('presupuesto')+$adendas + $gastado;
                        
                        $difvspresupuesto = $categoria->sum('presupuesto')+$adendas + $gastado - $costoproyectado;
                        $sheet->cells('G'.$cellindex++, function($cells) use($costoproyectado) {
                            $cells->setValue($costoproyectado);
                        }); 
                        
                    }
                    
                    $sheet->cells('A'.$cellindex, function($cells) {
                        $cells->setValue("Total: ");
                        $cells->setAlignment('right');
                        $cells->setFontWeight('bold');
                    }); 
                    
                    $sheet->cells('B'.$cellindex, function($cells) use($cuentas) {
                        $cells->setValue($cuentas->sum('presupuesto'));
                        $cells->setFontWeight('bold');
                    }); 
                    
                    if($adencuenta==null){
                        $adencuenta = 0;
                    }else{
                        $adencuenta = $adencuenta->sum('monto_nuevo');
                    }
                    $sheet->cells('C'.$cellindex, function($cells) use($adencuenta) {
                        $cells->setValue($adencuenta);
                        $cells->setFontWeight('bold');
                    }); 
                    
                    $sheet->cells('D'.$cellindex, function($cells) use($cuentas,$adencuenta) {
                        $cells->setValue($cuentas->sum('presupuesto')+ $adencuenta);
                        $cells->setFontWeight('bold');
                    }); 
                    
                    if($compcuenta==null){
                        $compcuenta = 0;
                    }else{
                        $compcuenta = $compcuenta->sum('comp');
                    }
                    $sheet->cells('E'.$cellindex, function($cells) use($compcuenta) {
                        $cells->setValue($compcuenta);
                        $cells->setFontWeight('bold');
                    }); 
                    
                    if($gastcuenta==null){
                        $gastcuenta = 0;
                    }else{
                        $gastcuenta = $gastcuenta->sum('gast');
                    }
                    if($plancuenta==null){
                        $plancuenta = 0;
                    }else{
                        $plancuenta = $plancuenta->sum('plan');
                    }
                    $gastcuenta+=$plancuenta;
                    $sheet->cells('F'.$cellindex, function($cells) use($gastcuenta) {
                        $cells->setValue($gastcuenta);
                        $cells->setFontWeight('bold');
                    }); 
                    $sheet->cells('G'.$cellindex, function($cells) use($cuentas,$cellindex) {
                        $index = $cuentas->groupBy('categoria')->count();
                        $cells->setValue('=sum(G'.($cellindex-$index).':G'.($cellindex-1).')');
                        $cells->setFontWeight('bold');
                    }); 
                    $cellindex++;
                    $cellindex++;
                }
                
                $values = array('B6:G'.$cellindex => '"$"#,##0.00');
                $sheet->setColumnFormat($values);
                
            });
        })->export('xls');
    }
      public function method1($a,$b) 
      {
        return (substr($a[2]["id_cuenta"],-3) <= substr($b[2]["id_cuenta"],-3)) ? -1 : 1;
      } 
    public function informesHome (Request $request)
    {
            $facturas = DB::table('cuentas')
            ->Join('facturas', 'facturas.id_cuenta', '=', 'cuentas.id_cuenta')                    
            ->Where('facturas.id_centro', '=', $request->centro)  
            ->select('cuentas.id_cuenta as id_cuenta','cuentas.nombre_cuenta as nombre_cuenta','cuentas.id_padre as id_padre')->groupBy('cuentas.id_cuenta')->groupBy('cuentas.nombre_cuenta')->groupBy('cuentas.id_padre')->orderBy('cuentas.id_cuenta')->get();
            $presupuesto = DB::table('cuentas')
            ->Join('presupuesto_y_avance', 'presupuesto_y_avance.id_cuenta', '=', 'cuentas.id_cuenta')                    
            ->Where('presupuesto_y_avance.id_centro_contable', '=', $request->centro)  
            ->select('cuentas.id_cuenta as id_cuenta','cuentas.nombre_cuenta as nombre_cuenta','cuentas.id_padre as id_padre')->groupBy('cuentas.id_cuenta')->groupBy('cuentas.nombre_cuenta')->groupBy('cuentas.id_padre')->orderBy('cuentas.id_cuenta')->get();
            $planilla = DB::table('cuentas')
            ->Join('planilla', 'planilla.id_cuenta', '=', 'cuentas.id_cuenta')                    
            ->Where('planilla.id_centro', '=', $request->centro)  
            ->select('cuentas.id_cuenta as id_cuenta','cuentas.nombre_cuenta as nombre_cuenta','cuentas.id_padre as id_padre')->groupBy('cuentas.id_cuenta')->groupBy('cuentas.nombre_cuenta')->groupBy('cuentas.id_padre')->orderBy('cuentas.id_cuenta')->get();
            $adendas = DB::table('cuentas')
            ->Join('items_detalle', 'items_detalle.id_cuenta', '=', 'cuentas.id_cuenta')                    
            ->Join('items', 'items.id', '=', 'items_detalle.id_item')                    
            ->Join('ajustes', 'items.id_ajuste', '=', 'ajustes.id')                    
            ->Join('adendas', 'adendas.id', '=', 'ajustes.id_adenda')                    
            ->Where('adendas.id_centro', '=', $request->centro)  
            ->select('cuentas.id_cuenta as id_cuenta','cuentas.nombre_cuenta as nombre_cuenta','cuentas.id_padre as id_padre')->groupBy('cuentas.id_cuenta')->groupBy('cuentas.nombre_cuenta')->groupBy('cuentas.id_padre')->orderBy('cuentas.id_cuenta')->get();
            $manual = DB::table('contab_cuentas_centros')
            ->Join('contab_cuentas', 'contab_cuentas_centros.cuenta_id', '=', 'contab_cuentas.id')         
            ->Join('cuentas', 'contab_cuentas.codigo', '=', 'cuentas.id_cuenta')         
            ->Where('contab_cuentas_centros.centro_id', '=', $request->centro)  
            ->select('contab_cuentas.codigo as id_cuenta','contab_cuentas.nombre as nombre_cuenta','cuentas.id_padre as id_padre')->groupBy('contab_cuentas.codigo')->groupBy('contab_cuentas.nombre')->groupBy('cuentas.id_padre')->orderBy('contab_cuentas.codigo')->get();

            $arreglo=[];
            $fact=[];
            $divf=[];
            $pres=[];
            $plan=[];
            $aden=[];
            $man=[];
            $divisorAnt="";
            foreach($facturas as $row_fact){
                $cuentasf = DB::table('cuentas')->where('id_cuenta', $row_fact->id_padre)->first();
                $divisorf = DB::table('cuentas')->where('id_cuenta','=', substr($cuentasf->id_cuenta,0,-3).'00.')->first();
                if(!empty($divisorf)){
                $divisor=$divisorf->id_cuenta;
                if($divisorAnt!==$divisor){
                    $divf=array('id_cuenta'=>$divisorf->id_cuenta,'nombre_cuenta'=>$divisorf->nombre_cuenta);
                    array_push($arreglo,$divf);
                }
                $fact= array('id_cuenta'=>$row_fact->id_cuenta, 'nombre_cuenta'=>$row_fact->nombre_cuenta);
                array_push($arreglo,$fact);
                $divisorAnt=$divisorf->id_cuenta;
                }
            }
            foreach($presupuesto as $row_pre){
                $cuentaspre = DB::table('cuentas')->where('id_cuenta', $row_pre->id_padre)->first();
                $divisorpre = DB::table('cuentas')->where('categoria','=','Divisor')->where('id_cuenta','like', substr($cuentaspre->id_cuenta,0,-3).'%')->first();
                if(!empty($divisorpre)){
                if($divisorAnt!==$divisorpre->id_cuenta){
                    $divf=array('id_cuenta'=>$divisorpre->id_cuenta,'nombre_cuenta'=>$divisorpre->nombre_cuenta);
                    array_push($arreglo,$divf);
                }                
                $pres= array('id_cuenta'=>$row_pre->id_cuenta, 'nombre_cuenta'=>$row_pre->nombre_cuenta);
                array_push($arreglo,$pres);
                $divisorAnt=$divisorf->id_cuenta;
                }
            }
            foreach($planilla as $row_plan){
                $cuentasplan = DB::table('cuentas')->where('id_cuenta', $row_plan->id_padre)->first();
                $divisorplan = DB::table('cuentas')->where('categoria','=','Divisor')->where('id_cuenta','like', substr($cuentasplan->id_cuenta,0,-3).'%')->first();
                if(!empty($divisorplan)){
                if($divisorAnt!==$divisorplan->id_cuenta){
                    $divf=array('id_cuenta'=>$divisorplan->id_cuenta,'nombre_cuenta'=>$divisorplan->nombre_cuenta);
                    array_push($arreglo,$divf);
                }                
                $plan= array('id_cuenta'=>$row_plan->id_cuenta, 'nombre_cuenta'=>$row_plan->nombre_cuenta);
                array_push($arreglo, $plan);
                $divisorAnt=$divisorf->id_cuenta;
                }
            }
            foreach($adendas as $row_aden){
                $cuentasaden = DB::table('cuentas')->where('id_cuenta', $row_aden->id_padre)->first();
                $divisoraden = DB::table('cuentas')->where('categoria','=','Divisor')->where('id_cuenta','like', substr($cuentasaden->id_cuenta,0,-3).'%')->first();
                if(!empty($divisoraden)){
                if($divisorAnt!==$divisoraden->id_cuenta){
                    $divf=array('id_cuenta'=>$divisoraden->id_cuenta,'nombre_cuenta'=>$divisoraden->nombre_cuenta);
                    array_push($arreglo,$divf);
                }                
                $aden= array('id_cuenta'=>$row_aden->id_cuenta, 'nombre_cuenta'=>$row_aden->nombre_cuenta);
                array_push($arreglo,$aden);
                $divisorAnt=$divisorf->id_cuenta;
                }
            }
            foreach($manual as $row_man){
                $cuentasman = DB::table('cuentas')->where('id_cuenta', $row_man->id_padre)->first();
                $divisorman = DB::table('cuentas')->where('categoria','=','Divisor')->where('id_cuenta','like', substr($cuentasman->id_cuenta,0,-3).'%')->first();
                if(!empty($divisorman)){
                if($divisorAnt!==$divisorman->id_cuenta){
                    $divf=array('id_cuenta'=>$divisorman->id_cuenta,'nombre_cuenta'=>$divisorman->nombre_cuenta);
                    array_push($arreglo,$divf);
                }                
                $man= array('id_cuenta'=>$row_man->id_cuenta, 'nombre_cuenta'=>$row_man->nombre_cuenta);
                array_push($arreglo,$man);
                $divisorAnt=$divisorf->id_cuenta;
                }
            }            
            $duplicates = array_values(array_map("unserialize", array_unique(array_map("serialize", $arreglo))));
            $arreglo_item1=$this->ordenado($duplicates,4);
            $arreglo_item2=$this->ordenado($arreglo_item1,3);
            $arreglo_item3=$this->ordenado($arreglo_item2,2);
            $arr = (object)array_map(function($arreglo_item3) { return is_array($arreglo_item3) ? (object)$arreglo_item3 :  $arreglo_item3;  }, $arreglo_item3);
            Excel::create('informe_codificador', function($excel) use($request, $arr)  {       
            $excel->sheet('Hoja 1', function($sheet) use($request, $arr){
                $sheet->setAutoSize(true);
                $sheet->mergeCells('A1:D1');        
                $sheet->cells('A1', function($cells) {
                    $cells->setValue('Informe Codificador de Proyectos');
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                    $cells->setFontSize(14);
                });
                
                $sheet->cells('A2', function($cells) {
                    $cells->setValue('Empresa:');
                });
                $sheet->cells('B2', function($cells) use($request) {
                    $cells->setValue(CentroContable::find($request->centro)->nombre_centro);
                });
                
                $sheet->cells('A3', function($cells) {
                    $cells->setValue('Proyecto:');
                });
                $sheet->cells('B3', function($cells) use($request) {
                    $cells->setValue(CentroContable::find($request->centro)->nombre_proyecto);
                });
                
                $sheet->cells('A4', function($cells) {
                    $cells->setValue('Encargado:');
                });
                $sheet->cells('B4', function($cells) use($request) {
                    $cells->setValue("");
                });
                
                $sheet->cells('D2',function($cells){
                    $cells->setValue("Generado por:");
                });
                
                $sheet->cells('F2',function($cells){
                    $cells->setValue(User::find(auth()->id())->name);
                });
                
                $sheet->cells('D3',function($cells){
                    $cells->setValue("Fecha del reporte:");
                });
                
                $sheet->cells('F3',function($cells){
                    $cells->setValue(Carbon::now()->format('d/m/Y'));
                });
                
                
                $sheet->cells('A4',function($cells){
                    $cells->setValue("Codigo");
                });
                
                $sheet->cells('B4',function($cells) use ($request){
                    $cells->setValue('Cuenta');
                });
                $i=0;  
                $cellindex = 6;
                foreach($arr as $key_r){
                    $idcuenta=$key_r->id_cuenta;
                    $nombre=$key_r->nombre_cuenta;
                    if(substr($key_r->id_cuenta, -3)=="00."){
                        $sheet->row($cellindex, function($row) {
                            $row->setBackground('#E5E5E5');
                        });
                    }
                    $sheet->cells('A'.$cellindex, function($cells) use ($idcuenta){
                        $cells->setValue($idcuenta);
                    });
                    $sheet->cells('B'.$cellindex, function($cells) use ($nombre){
                        $cells->setValue($nombre);
                    });                
                    $cellindex++;
                    $i++;
                }    
               
            });
        })->export('xls');
    }
    public function ordenado($array,$item){
            $j=0;
            $flag = true;
            $temp=0;
            while ( $flag )
            {
              $flag = false;
              for( $j=0;  $j < count($array)-1; $j++)
              {
                $array_cuenta_first=explode(".",$array[$j]['id_cuenta']);
                $array_cuenta_next=explode(".",$array[$j+1]['id_cuenta']);  
                if ((int)$array_cuenta_first[$item]>(int)$array_cuenta_next[$item] )
                {
                      $temp = $array[$j];
                      $array[$j] = $array[$j+1];
                      $array[$j+1]=$temp;                  
                      $a=1;
                      $flag = true;
                }                 
                }
            }
            return $array;
    }   
}