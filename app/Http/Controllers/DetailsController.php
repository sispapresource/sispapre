<?php

namespace App\Http\Controllers;

use App\Adenda;
use App\CentroContable;
use App\Cuenta;
use App\Factura;
use App\OrdenCompra;
use App\Planilla;
use App\PresupuestoAvance;
use App\Trazabilidad;
use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\ConfiguracionFormula;

class DetailsController extends Controller
{
    /**
    * Create a new controller getGraficoCuentas.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request){
        
        $content = $request->all();
        $items = null;
        $resp = null;
        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';
        
        $centroContable = CentroContable::find($idCentro);
        
        // load id_cuentas in presupuesto_y_avance from facturas, ordenes and planilla
        
        $query = Factura::select('id_cuenta')->where('id_centro', $idCentro)->get();
        foreach ($query as $factura)
        PresupuestoAvance::firstOrCreate(['id_centro_contable' => $idCentro, 'id_cuenta'=> $factura->id_cuenta]);
        $query = OrdenCompra::select('id_cuenta')->where('id_centro', $idCentro)->get();
        foreach ($query as $orden)
        PresupuestoAvance::firstOrCreate(['id_centro_contable' => $idCentro, 'id_cuenta'=> $orden->id_cuenta]);
        $query = Planilla::select('id_cuenta')->where('id_centro', $idCentro)->get();
        foreach ($query as $planilla)
        PresupuestoAvance::firstOrCreate(['id_centro_contable' => $idCentro, 'id_cuenta'=> $planilla->id_cuenta]);
        $query = DB::table('items_detalle')
        ->join('items', 'items.id', '=','items_detalle.id_item')
        ->join('ajustes', 'ajustes.id', '=','items.id_ajuste')
        ->join('adendas', 'adendas.id', '=','ajustes.id_adenda')
        ->where('adendas.id_centro','=',$idCentro)
        ->select('items_detalle.id_cuenta')->get();
        foreach ($query as $adenda)
        PresupuestoAvance::firstOrCreate(['id_centro_contable' => $idCentro, 'id_cuenta'=> $adenda->id_cuenta]);
        
        // fin load
        
        if(isset($idCentro)){
            
            $query = DB::table('presupuesto_y_avance')->where("id_centro_contable",$idCentro);
            $query = $query->get();
            
            foreach ($query as $cuenta){
                
                $suma_oc = OrdenCompra::where('id_centro', $idCentro)->
                where('id_cuenta', $cuenta->id_cuenta)->
                sum('monto_compra');
                
                $suma_fa = Factura::where('id_centro', $idCentro)->
                where('id_cuenta', $cuenta->id_cuenta)->
                sum('monto');

                $sum_pla = Planilla::where('id_centro',$idCentro)
                ->where('id_cuenta',$cuenta->id_cuenta)
                ->sum('monto');
                
                $comp = $suma_oc - $suma_fa;
                $gastado = $suma_fa + $sum_pla;
                
                $adendas = app('App\Http\Controllers\AdendaController')->getMontoAdendasByProyectoAndCuenta($idCentro,$cuenta->id_cuenta);
                
                $cuentaEntity = Cuenta::find($cuenta->id_cuenta);
                
                $resp['id_cuenta']=$cuenta->id_cuenta;
                $resp['nombre_cuenta']= $cuentaEntity->nombre_cuenta ;
                $resp['presupuesto']=$cuenta->presupuesto;
                $resp['adendas']=$adendas;
                $resp['presupuesto_total']=$cuenta->presupuesto+$adendas;
                $resp['porcentaje_avance']= $cuenta->porcentaje_avance;
                $resp['porcentaje_teorico']= $cuenta->porcentaje_teorico;
                $resp['presupuestoxavance']=1;//$cuenta->presupuesto * ($cuenta->porcentaje_avance / 100 );
                $resp['comp']=$comp;
                $resp['gastado']=$gastado;
                $resp['presupuestoxgastar']=1;//($cuenta->presupuesto * ($cuenta->porcentaje_avance / 100 ))-$gastado; // presupuestoxavance-gastado
                $resp['diferenciavspresupuesto']= 1;// ($comp / $resp['presupuestoxavance'])*100;
                $resp['nivel']= $cuentaEntity->nivel;
                $resp['id_padre']= $cuentaEntity->id_padre;
                
                $items[] = $resp;
            }
            
            $items[] = $resp;
            return view('detalles-cuentas')->with(['cuentas'=>$items,'nombre_centro'=>$centroContable->nombre_centro,'id_centro'=>$idCentro,'idc'=>$centroContable->id_centro ]);
        }else
        return view('home');
    }
    public function verTrazabilidad(){
        $query = DB::table('trazabilidades')
        ->join('users', 'trazabilidades.id_usuario', '=','users.id')
        ->join('centros_contables', 'trazabilidades.id_centro', '=','centros_contables.id_centro')
        ->select('trazabilidades.id as id',
                 'trazabilidades.id_cuenta as id_cuenta',
                 'trazabilidades.id_centro as id_centro',
                 'trazabilidades.id_usuario as id_usuario',
                 'trazabilidades.fecha as fecha',
                 'trazabilidades.presupuesto as presupuesto',
                 'trazabilidades.old_presupuesto as old_presupuesto',
                 'centros_contables.nombre_centro as nombre_centro',
                 'users.name as nombre_usuario')
                 ->orderBy('trazabilidades.fecha')->paginate(10);


        return view('trazabilidad')->with(['query'=>$query] );


    }
    public function editarPresupuesto(Request $request){
        
        $content = $request->all();
        $items = null;
        $resp = null;
        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';
        
        $centroContable = CentroContable::find($idCentro);
        
        // load id_cuentas in presupuesto_y_avance from facturas, ordenes and planilla
        
        $query = Factura::select('id_cuenta')->where('id_centro', $idCentro)->get();
        foreach ($query as $factura)
        PresupuestoAvance::firstOrCreate(['id_centro_contable' => $idCentro, 'id_cuenta'=> $factura->id_cuenta]);
        $query = OrdenCompra::select('id_cuenta')->where('id_centro', $idCentro)->get();
        foreach ($query as $orden)
        PresupuestoAvance::firstOrCreate(['id_centro_contable' => $idCentro, 'id_cuenta'=> $orden->id_cuenta]);
        $query = Planilla::select('id_cuenta')->where('id_centro', $idCentro)->get();
        foreach ($query as $planilla)
        PresupuestoAvance::firstOrCreate(['id_centro_contable' => $idCentro, 'id_cuenta'=> $planilla->id_cuenta]);
        $query = DB::table('items_detalle')
        ->join('items', 'items.id', '=','items_detalle.id_item')
        ->join('ajustes', 'ajustes.id', '=','items.id_ajuste')
        ->join('adendas', 'adendas.id', '=','ajustes.id_adenda')
        ->where('adendas.id_centro','=',$idCentro)
        ->select('items_detalle.id_cuenta')->get();
        foreach ($query as $adenda)
        PresupuestoAvance::firstOrCreate(['id_centro_contable' => $idCentro, 'id_cuenta'=> $adenda->id_cuenta]);
        
        // fin load
        
        if(isset($idCentro)){
            
            $query = DB::table('presupuesto_y_avance')->where("id_centro_contable",$idCentro);
            $query = $query->get();
            
            foreach ($query as $cuenta){
                
                $suma_oc = OrdenCompra::where('id_centro', $idCentro)->
                where('id_cuenta', $cuenta->id_cuenta)->
                sum('monto_compra');
                
                $suma_fa = Factura::where('id_centro', $idCentro)->
                where('id_cuenta', $cuenta->id_cuenta)->
                sum('monto');

                $sum_pla = Planilla::where('id_centro',$idCentro)
                ->where('id_cuenta',$cuenta->id_cuenta)
                ->sum('monto');
                
                $comp = $suma_oc - $suma_fa;
                $gastado = $suma_fa + $sum_pla;
                
                $adendas = app('App\Http\Controllers\AdendaController')->getMontoAdendasByProyectoAndCuenta($idCentro,$cuenta->id_cuenta);
                
                $cuentaEntity = Cuenta::find($cuenta->id_cuenta);
                
                $resp['id_cuenta']=$cuenta->id_cuenta;
                $resp['nombre_cuenta']= $cuentaEntity->nombre_cuenta ;
                $resp['presupuesto']=$cuenta->presupuesto;
                $resp['adendas']=$adendas;
                $resp['presupuesto_total']=$cuenta->presupuesto+$adendas;
                $resp['porcentaje_avance']= $cuenta->porcentaje_avance;
                $resp['porcentaje_teorico']= $cuenta->porcentaje_teorico;
                $resp['presupuestoxavance']=1;//$cuenta->presupuesto * ($cuenta->porcentaje_avance / 100 );
                $resp['comp']=$comp;
                $resp['gastado']=$gastado;
                $resp['presupuestoxgastar']=1;//($cuenta->presupuesto * ($cuenta->porcentaje_avance / 100 ))-$gastado; // presupuestoxavance-gastado
                $resp['diferenciavspresupuesto']= 1;// ($comp / $resp['presupuestoxavance'])*100;
                $resp['nivel']= $cuentaEntity->nivel;
                $resp['id_padre']= $cuentaEntity->id_padre;
                
                $items[] = $resp;
            }
            
            $items[] = $resp;
            $cuentasAsignables = PresupuestoAvance::where('id_centro_contable',$idCentro)->pluck('id_cuenta');
            $cuentasAsignables = Cuenta::where('nivel',2)->whereNotIn('id_cuenta',$cuentasAsignables)->pluck('id_cuenta','id_cuenta');
            return view('editar-presupuesto')->with(['cuentasAsignables'=>$cuentasAsignables,'cuentas'=>$items,'nombre_centro'=>$centroContable->nombre_centro,'id_centro'=>$idCentro,'idc'=>$centroContable->id_centro ]);
        }else
        return view('home');
    }
    public function agregarCuenta(CentroContable $centro, Request $request){
        $this->validate($request,[
            'agregar_cuenta'=>'required'
        ]);
        PresupuestoAvance::create([
                'id_cuenta'=>$request->agregar_cuenta,
                'id_centro_contable'=>$centro->id_centro,
                'presupuesto'=>0,
                'porcentaje_avance'=>0,
                'porcentaje_teorico'=>0
            ]);
        $request->session()->flash('cuentas', $request->agregar_cuenta);
        return back();
        //return route('editar.presupuesto',['id_centro'=>$request->idCentro]);
    }

    public function guardarEditPresupuesto(Request $request){
        $oldpresupuesto= DB::table('presupuesto_y_avance')->where('id_centro_contable','=',$request->id_centro)->where('id_cuenta','=',$request->id)->first();
        $return = PresupuestoAvance::where('id_cuenta',$request->id)
        ->where('id_centro_contable',$request->id_centro)
        ->update(['presupuesto'=>floatval($request->Presupuesto_original_2)]);
        $trazabilidad = new Trazabilidad;
        $trazabilidad->id_centro = $request->id_centro;
        $trazabilidad->id_cuenta = $request->id;
        $trazabilidad->id_usuario = Auth::id();
        $trazabilidad->presupuesto = floatval($request->Presupuesto_original_2);
        $trazabilidad->old_presupuesto=floatval($oldpresupuesto->presupuesto);
        $trazabilidad->save();
        if($return)
            return response()->json(['code' => 200, 'status'=>'sucess']);
        else
            return response()->json(['code' => 401, 'status'=>'sucess']);
    }
    public function getTotalesCentro(CentroContable $centro, Request $request){
        $message = [];
        $message['presupuestoOriginal'] = floatval($centro->presupuesto());
        $message['adendas'] = floatval($centro->montoAdendas());
        $message['presupuestoTotal'] = floatval($centro->presupuesto()) + floatval($centro->montoAdendas());
        $message['comp'] = floatval($centro->ordenesCompra()->sum('monto_compra'));
        $message['gastado'] = floatval($centro->gastado());
        $config = ConfiguracionFormula::where('slug','presupuestoxgastar')->first();
        if($config->id_valor == 1)
             $xgastar = floatval($message['presupuestoTotal']) - floatval($message['gastado']);
        else if($config->id_valor == 2)
            $xgastar = floatval($message['presupuestoTotal']) - (floatval($message['gastado'])+floatval($message['comp']));
        $message['porGastar'] = $xgastar;
        return response()->json($message);
    }

    public function getCuentas(Request $request){

//        dd($request->codigoFilter);
        $table='presupuesto_y_avance';
        $content = $request->all();
        $agregados = [];
        
        $identificador_padre=$identificador_hijo=-1;
        
        $codigoFilter = isset($content['codigoFilter'])?$content['codigoFilter']: '';
        $cuentaFilter = isset($content['cuentaFilter'])?$content['cuentaFilter']: '';
        $categoriaFilter = isset($content['categoriaFilter'])?$content['categoriaFilter']: '';
        $dateFilterDesde = isset($content['dateFilterDesde'])?$content['dateFilterDesde']: '';
        $dateFilterHasta = isset($content['dateFilterHasta'])?$content['dateFilterHasta']: '';
        
        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';
        
        $items = null;
        
        $keyOrder = "cast(SUBSTRING_INDEX(SUBSTRING_INDEX(".$table.".id_cuenta, '.', 3 ),'.',-1) as unsigned),
        cast(SUBSTRING_INDEX(SUBSTRING_INDEX(".$table.".id_cuenta, '.', 4 ),'.',-1) as unsigned),
        cast(SUBSTRING_INDEX(SUBSTRING_INDEX(".$table.".id_cuenta, '.', 5 ),'.',-1) as unsigned)";
        
        if(isset($categoriaFilter) && $categoriaFilter!= ''){
            $condition = array_filter(explode(",", $categoriaFilter));
            
            $query = DB::table($table)
            ->join('cuentas', $table.'.id_cuenta', '=', 'cuentas.id_cuenta')
            ->where($table.".id_centro_contable",$idCentro)
            ->where(function($q) use ($condition){
                foreach($condition as $value){
                    $q->orWhere('cuentas.categoria', '=', $value);
                }
            })
            ->orderBy(\DB::raw($keyOrder))->get();
        }
        else{
            $query = DB::table($table)
            ->where("id_centro_contable",$idCentro)
            ->orderBy(\DB::raw($keyOrder))
            ->get();
        }
        $index = 0;
        $nivel0 = [];
        $nivel1 = [];
        foreach ($query as $cuenta){
            
            $addTrue = true;
            $cuentaEntity = Cuenta::find($cuenta->id_cuenta);
            
            if(isset($codigoFilter) && $codigoFilter != ''){
                if(stripos($cuenta->id_cuenta,$codigoFilter)===false)
                    $addTrue = false;
            }
            if(isset($cuentaFilter) && $cuentaFilter != ''){
                if(stripos($cuentaEntity->nombre_cuenta,$cuentaFilter)===false)
                    $addTrue = false;
                
            }
            
            if($addTrue){
                if($dateFilterDesde=='')
                    $dateFilterDesde='12/12/0000';
                //                    $dateFilterDesde='12/12/2022';
                if($dateFilterHasta=='')
                    $dateFilterHasta='01/01/2100';
                
                $desde = Carbon::createFromFormat('m/d/Y', $dateFilterDesde)->format('Y/m/d');
                $hasta = Carbon::createFromFormat('m/d/Y', $dateFilterHasta)->format('Y/m/d');
                
                $comp = OrdenCompra::where('id_centro', $idCentro)->where('id_cuenta', $cuenta->id_cuenta)
                ->whereDate('fecha_transaccion','>=', $desde)
                ->whereDate('fecha_transaccion','<=', $hasta)
                ->sum('monto_compra');
                $gastado = Factura::where('id_centro', $idCentro)->where('id_cuenta', $cuenta->id_cuenta)
                ->whereDate('fecha_transaccion','>=', $desde)
                ->whereDate('fecha_transaccion','<=', $hasta)
                ->sum('monto');
                

                $planilla = Planilla::where('id_centro', $idCentro)->where('id_cuenta', $cuenta->id_cuenta)
                ->whereDate('fecha_transaccion','>=', $desde)
                ->whereDate('fecha_transaccion','<=', $hasta)
                ->sum('monto');

                
                $adendas = app('App\Http\Controllers\AdendaController')->getMontoAdendasByProyectoAndCuenta($idCentro,$cuenta->id_cuenta);
                
                if($cuentaEntity->nivel==2 && $identificador_hijo!=$cuentaEntity->id_padre && !in_array($cuenta->id_cuenta,$agregados)){
                    
                    $identificador_hijo=$cuentaEntity->id_padre;
                    
                    $cuentaEntity2 = Cuenta::find($cuentaEntity->id_padre);
                    
                    if($cuentaEntity2 && $identificador_padre!=$cuentaEntity2->id_padre && !in_array($cuentaEntity->id_padre,$agregados)){ //cambio de padre
                        
                        $cuentaEntity3 = Cuenta::find($cuentaEntity2->id_padre);
                        
                        if(empty($cuentaEntity3)){
                            $identificador_padre=$cuentaEntity2->id_padre;
                        
                            $resp['id_cuenta']='';
                            $resp['nombre_cuenta']= '';
                            $resp['presupuesto']=0;
                            $resp['adendas']=0;
                            $resp['presupuesto_total']=0;
                            $resp['porcentaje_avance']= '--';
                            $resp['porcentaje_teorico']= '--';
                            $resp['presupuestoxavance']= '--';
                            $resp['comp']=0;
                            $resp['gastado']=0;
                            $resp['presupuestoxgastar']=0; // presupuestoxavance-gastado
                            $resp['costoproyectado']=0;
                            $resp['diferenciavspresupuesto']=0;
                            $resp['nivel']= '';
                            $resp['id_padre']= '';
                            $resp['isleaf']= false;
                        }
                        else {
                            $identificador_padre=$cuentaEntity2->id_padre;
                            
                            $resp['id_cuenta']=$cuentaEntity3->id_cuenta;
                            $resp['nombre_cuenta']= $cuentaEntity3->nombre_cuenta;
                            $resp['presupuesto']=0;
                            $resp['adendas']=0;
                            $resp['presupuesto_total']=0;
                            $resp['porcentaje_avance']= '--';
                            $resp['porcentaje_teorico']= '--';
                            $resp['presupuestoxavance']= '--';
                            $resp['comp']=0;
                            $resp['gastado']=0;
                            $resp['presupuestoxgastar']=0; // presupuestoxavance-gastado
                            $resp['costoproyectado']=0;
                            $resp['diferenciavspresupuesto']=0;
                            $resp['nivel']= $cuentaEntity3->nivel;
                            $resp['id_padre']= $cuentaEntity3->id_padre;
                            $resp['isleaf']= false;

                            if($cuentaEntity3->nivel == 0){
                                $nivel0[] = $index;
                            }else if($cuentaEntity3->nivel ==1){
                                $nivel1[] = $index;
                            }
                        }                          
                        $agregados[] = $resp['id_cuenta']; 
                        
                        $index++;

                        $items[] = $resp;

                    }
                    if($cuentaEntity2){
                        $agregados[] = $cuentaEntity2->id_cuenta;
                        $resp['id_cuenta']=$cuentaEntity2->id_cuenta;
                        $resp['nombre_cuenta']= $cuentaEntity2->nombre_cuenta ;
                        $resp['presupuesto']=0;
                        $resp['adendas']=0;
                        $resp['presupuesto_total']=0;
                        $resp['porcentaje_avance']= '--';
                        $resp['porcentaje_teorico']= '--';
                        $resp['presupuestoxavance']= '--';
                        $resp['comp']=0;
                        $resp['gastado']=0;
                        $resp['presupuestoxgastar']=0; // presupuestoxavance-gastado
                        $resp['costoproyectado']=0;
                        $resp['diferenciavspresupuesto']=0;
                        $resp['nivel']= $cuentaEntity2->nivel;
                        $resp['id_padre']= $cuentaEntity2->id_padre;
                        $resp['isleaf']= false;
                        
                        if($cuentaEntity2->nivel == 0){
                            $nivel0[] = $index;
                        }else if($cuentaEntity2->nivel ==1){
                            $nivel1[] = $index;
                        }

                        $index++;

                        $items[] = $resp;
                    }
                }
                if($cuentaEntity->nivel==1){
                    
                    $identificador_hijo=$cuentaEntity->id_cuenta;
                    
                    if($identificador_padre!=$cuentaEntity->id_padre && !in_array($cuentaEntity->id_padre,$agregados)){
                        
                        $identificador_padre=$cuentaEntity->id_padre;
                        $cuentaEntity2 = Cuenta::find($cuentaEntity->id_padre);

                        $agregados[] = $cuentaEntity2->id_cuenta;
                        $resp['id_cuenta']=$cuentaEntity2->id_cuenta;
                        $resp['nombre_cuenta']= $cuentaEntity2->nombre_cuenta;
                        $resp['presupuesto']=0;
                        $resp['adendas']=0;
                        $resp['presupuesto_total']=0;
                        $resp['porcentaje_avance']= '--';
                        $resp['porcentaje_teorico']= '--';
                        $resp['presupuestoxavance']= '--';
                        $resp['comp']=0;
                        $resp['gastado']=0;
                        $resp['presupuestoxgastar']=0;
                        $resp['costoproyectado']=0;
                        $resp['diferenciavspresupuesto']=0;
                        $resp['nivel']= $cuentaEntity2->nivel;
                        $resp['id_padre']= $cuentaEntity2->id_padre;
                        $resp['isleaf']= false;
                        
                        if($cuentaEntity2->nivel == 0){
                            $nivel0[] = $index;
                        }else if($cuentaEntity2->nivel ==1){
                            $nivel1[] = $index;
                        }
                        
                        $index++;

                        $items[] = $resp;
                    }
                }
                $agregados[] = $cuenta->id_cuenta;
                $resp['id_cuenta']=$cuenta->id_cuenta;
                $resp['nombre_cuenta']= $cuentaEntity->nombre_cuenta;
                $resp['presupuesto']=$cuenta->presupuesto;
                $resp['adendas']=$adendas;
                $resp['presupuesto_total']=$cuenta->presupuesto+$adendas;
                $resp['porcentaje_avance']=0+$cuenta->porcentaje_avance;
                $resp['porcentaje_teorico']=0+$cuenta->porcentaje_teorico;
                $resp['comp']=0+$comp;
                $resp['gastado']=0+$gastado+$planilla;
                
                $config = ConfiguracionFormula::where('slug','presupuestoxgastar')->first();
                if($config->id_valor == 1)
                    $resp['presupuestoxgastar']=$resp['presupuesto_total']-$resp['gastado'];
                else if($config->id_valor == 2)
                    $resp['presupuestoxgastar']=$resp['presupuesto_total']-($resp['gastado']+$resp['comp']);

                $resp['presupuestoxavance']= ($cuenta->presupuesto+$adendas) * ($cuenta->porcentaje_avance / 100 );
                $resp['costoproyectado']= 0;
                if($resp['porcentaje_avance']>0)
                    $resp['costoproyectado']= ($resp['gastado'] / $resp['porcentaje_avance'])*100;
                else
                    $resp['costoproyectado']=$resp['presupuesto_total']+$resp['gastado'];
                $resp['diferenciavspresupuesto']= $resp['presupuesto_total']-$resp['costoproyectado'];          
                $resp['nivel']= $cuentaEntity->nivel;
                $resp['id_padre']= $cuentaEntity->id_padre;
                $resp['isleaf']= true;

                if($cuentaEntity->nivel == 0){
                    $nivel0[] = $index;
                }else if($cuentaEntity->nivel == 1){
                    $nivel1[] = $index;
                }

                $index++;

                $items[] = $resp;
                
            }
        }
        // dd($nivel0,$nivel1);
        // for ($i=0; $i<count($items); $i++) { // load data to items with nivel = 1
        foreach($nivel1 as $i) { // load data to items with nivel = 1
            // $countlevel2=0;
            for ($k=0; $k<count($items); $k++) {
                if($items[$i]['id_cuenta']==$items[$k]['id_padre']){
                    // $countlevel2++;
                    $items[$i]['presupuesto']+= $items[$k]['presupuesto'];
                    $items[$i]['adendas']+= $items[$k]['adendas'];
                    $items[$i]['presupuesto_total']+= $items[$k]['presupuesto_total'];
                    $items[$i]['presupuestoxavance']= floatval($items[$i]['presupuestoxavance'])+$items[$k]['presupuestoxavance'];
                    $items[$i]['comp']+= $items[$k]['comp'];
                    $items[$i]['gastado']+= $items[$k]['gastado'];
                    $items[$i]['presupuestoxgastar']+= $items[$k]['presupuestoxgastar'];
                    $items[$i]['costoproyectado']+= $items[$k]['costoproyectado'];
                    $items[$i]['diferenciavspresupuesto']+= $items[$k]['diferenciavspresupuesto'];
                    
                    $items[$i]['porcentaje_avance']= floatval($items[$i]['porcentaje_avance']) +  $items[$k]['presupuesto_total'] * ($items[$k]['porcentaje_avance']/100) ;
                    $items[$i]['porcentaje_teorico']= floatval($items[$i]['porcentaje_teorico']) +    $items[$k]['presupuesto_total'] * ( $items[$k]['porcentaje_teorico']/100);  
                }
            }
            
            $items[$i]['porcentaje_avance'] =  $items[$i]['presupuesto_total']==0? 100 : ( $items[$i]['porcentaje_avance'] / $items[$i]['presupuesto_total'])*100;
            $items[$i]['porcentaje_teorico'] =  $items[$i]['presupuesto_total']==0? 100 : ( $items[$i]['porcentaje_teorico']  / $items[$i]['presupuesto_total'])*100;  
            
        } 
        foreach($nivel0 as $i) { 
            // $countlevel1=0;        
            for ($k=0; $k<count($items); $k++) {
                if($items[$i]['id_cuenta']==$items[$k]['id_padre']){
                    // $countlevel1++;
                    $items[$i]['presupuesto']+= $items[$k]['presupuesto'];
                    $items[$i]['adendas']+= $items[$k]['adendas'];
                    $items[$i]['presupuesto_total']+= $items[$k]['presupuesto_total'];
                    $items[$i]['presupuestoxavance']= floatval($items[$i]['presupuestoxavance'])+$items[$k]['presupuestoxavance'];
                    $items[$i]['comp']+= $items[$k]['comp'];
                    $items[$i]['gastado']+= $items[$k]['gastado'];
                    $items[$i]['presupuestoxgastar']+= $items[$k]['presupuestoxgastar'];
                    $items[$i]['costoproyectado']+= $items[$k]['costoproyectado'];
                    $items[$i]['diferenciavspresupuesto']+= $items[$k]['diferenciavspresupuesto'];

                    $items[$i]['porcentaje_avance']= floatval($items[$i]['porcentaje_avance']) +  $items[$k]['presupuesto_total'] * ($items[$k]['porcentaje_avance']/100) ;
                    $items[$i]['porcentaje_teorico']= floatval($items[$i]['porcentaje_teorico']) +    $items[$k]['presupuesto_total'] * ( $items[$k]['porcentaje_teorico']/100);  
                }
            }
            
            $items[$i]['porcentaje_avance'] =   $items[$i]['presupuesto_total']== 0? 100 : ( $items[$i]['porcentaje_avance']/$items[$i]['presupuesto_total'])*100;
            $items[$i]['porcentaje_teorico'] =  $items[$i]['presupuesto_total']== 0? 100 : ( $items[$i]['porcentaje_teorico']  / $items[$i]['presupuesto_total'])*100;  
        }

        $cuentas = Cuenta::where('categoria','=','Divisor')->pluck('nombre_cuenta','id_cuenta')->toArray();
        
        if($request->ajax()){
            return response()->json([
                'cuentas' => $items,
                'cuentasDivisoras'=>$cuentas
                ]);
            }
            
        }
        
        public function getCuentasDivisoras(){
            $cuentas = Cuenta::where('categoria','=','Divisor')->pluck('nombre_cuenta','id_cuenta')->toArray();
            
            return response()->json(['cuentas'=>$cuentas]);
        }
        public function getcuentasAutocompletar(Request $request){
            $content = $request->all();
            $idCentro = isset($content['idCentro'])?$content['idCentro']: '';            
            $busqueda = isset($content['busqueda'])?$content['busqueda']: '';            

            $cuentasP = PresupuestoAvance::where('id_centro_contable',$idCentro)->pluck('id_cuenta');
            $cuentas = DB::table('cuentas')->whereNotIn('id_cuenta', $cuentasP)->where('nivel', 2)->where('id_cuenta','like','%'.$busqueda.'%')->get();
            $cuentasAsignables='["';
            foreach ($cuentas as $key_cuentas) {
                $cuentasAsignables.=$key_cuentas->id_cuenta.'", "';
            }
            $cuentasAsignables.='"]';
            $cuentasAsignables=str_replace(', ""', ' ', $cuentasAsignables);
            return $cuentasAsignables;
        }        
        public function getGraficoCuentas(Request $request){
            $content = $request->all();
            $items = null;
            $idCentro = isset($content['idCentro'])?$content['idCentro']: '';
            $centroContable = CentroContable::find($idCentro);
            
            
            return view('detalles-cuentas-grafico')->with(['nombre_centro'=>$centroContable->nombre_centro,'id_centro'=>$idCentro] );
        }
        public function getDataGrafico($idCentro){
            $centroContable = CentroContable::find($idCentro);
            $keyOrder = "cast(SUBSTRING_INDEX(SUBSTRING_INDEX(id_cuenta, '.', 3 ),'.',-1) as unsigned) as T";
            
            $cuentas = DB::table('presupuesto_y_avance')
            ->select(\DB::raw($keyOrder))
            ->where('id_centro_contable',$idCentro)
            ->orderBy('T')->distinct('T')->get();
            
            foreach ($cuentas as $cuenta){
                $cuentaEntity = Cuenta::find('1.3.'.$cuenta->T.'.');
                $resp['nombre_cuenta']= $cuentaEntity->nombre_cuenta;
                $items[] = $resp;
                
                $resp2['presupuesto_original'] = PresupuestoAvance::where('id_centro_contable',$idCentro)
                ->where('id_cuenta', 'LIKE', '1.3.'.$cuenta->T.'.%')->sum('presupuesto');
                $cuentas_completas = DB::table('presupuesto_y_avance')
                ->select('id_cuenta','presupuesto','porcentaje_avance','porcentaje_teorico')
                ->where('id_centro_contable',$idCentro)->where('id_cuenta', 'LIKE', '1.3.'.$cuenta->T.'.%')->get();
                $resp3['presupuesto_x_avance']=0;
                $resp4['gastado_total'] = 0;
                foreach ($cuentas_completas as $cuenta){             
                    $resp2['presupuesto_original']+=app('App\Http\Controllers\AdendaController')->getMontoAdendasByProyectoAndCuenta($idCentro,$cuenta->id_cuenta);
                    $resp3['presupuesto_x_avance']+= $cuenta->presupuesto * ($cuenta->porcentaje_avance / 100 );
                    
                    $gastado = Factura::where('id_centro', $idCentro)->where('id_cuenta', $cuenta->id_cuenta)->sum('monto');
                    $planilla = Planilla::where('id_centro', $idCentro)->where('id_cuenta', $cuenta->id_cuenta)->sum('monto');
                    $resp4['gastado_total'] += $gastado+$planilla;
                }
                $items2[] = $resp2;
                $items3[] = $resp3;
                $items4[] = $resp4;
            }
            $result['cuentas']=$items;
            $result['presupuesto']=$items2;
            $result['p_avance']=$items3;
            $result['gastado']=$items4;
            
            return $result;
        }
        
    }
    