<?php

namespace App\Http\Controllers;

use App\Anticipo;
use App\CentroContable;
use App\Cobro;
use App\Factura;
use App\FacturaVenta;
use App\Pago;
use App\Planilla;
use App\OrdenCompra;
use App\Http\Requests;
use App\PresupuestoAvance;
use App\ConfiguracionFormula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//Ver amortizaciones pagos
use Illuminate\Support\Facades\Auth;
use App\User;
use Bican\Roles\Models\Role;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        return view('home');
    }

    public function getCentrosContables(Request $request){
        $anticipopagos = false;
        if(Auth::user()->can('ver.anticipopagos')){
            $anticipopagos = true;
        }
        $estadop = false;
        if(Auth::user()->can('ver.estado')){
            $estadop= true;
        }

        $docp = false;
        if(Auth::user()->can('ver.inactivos')){
            $docp= true;
        }

        $inactivos = false;
        if(Auth::user()->can('ver.inactivos')){
            $inactivos= true;
        }
        $nivel0=[];
        $index = 0;
        $add = true;
        $items = null;
        $content = $request->all();
        $identificador_padre=-1;
        $dateFilter = isset($content['dateFilter'])?$content['dateFilter']: '';
        $textFilter = isset($content['textFilter'])?$content['textFilter']: '';
        if(isset($textFilter) && $textFilter!= ''){
            $centros = DB::table('centros_contables')
                ->join('centro_contable_user', 'centros_contables.id_centro', '=', 'centro_contable_user.centro_contable_id')
                ->where('centro_contable_user.user_id',auth()->id())
                ->where('centros_contables.totalizador',0)
                ->where('centros_contables.nombre_centro','LIKE','%'.$textFilter.'%')
                ->orderBy('centros_contables.id_padre', 'asc')
                ->orderBy('centros_contables.id_centro', 'asc')->get();
        }else{
            $centros = DB::table('centros_contables')
                ->join('centro_contable_user', 'centros_contables.id_centro', '=', 'centro_contable_user.centro_contable_id')
                ->where('centros_contables.totalizador',0)
                ->where('centro_contable_user.user_id',auth()->id())
                ->orderBy('centros_contables.id_padre', 'asc')
                ->orderBy('centros_contables.id_centro', 'asc')->get();
        }

        foreach ($centros as $centro){
            $centro = CentroContable::find($centro->id_centro);
            if($centro->estado_id == 0){
                $centro->estado_id = 1;
                $centro->save();
            }
            if($centro->estado->nombre==="Inactivo" && !Auth::user()->can('ver.inactivos')){
                continue;
            }

            $ultimaActualizacion = DB::table('log_cambios')
                ->where("id_centro",$centro->id_centro)
                ->max('fecha_entrada');

            if(isset($dateFilter)){
                $add = false;
                $date_init = strtotime($dateFilter);
                $ua_comp = strtotime(isset($ultimaActualizacion)? $ultimaActualizacion:'1999-01-01 00:00:00');
                if($ua_comp > $date_init)
                    $add = true;
            }
            if($add){ //agregar
                if($centro->nivel==1 && $identificador_padre!=$centro->id_padre && $centro->id_padre!=0){
                    $identificador_padre=$centro->id_padre;
                    $centroEntity = CentroContable::find($centro->id_padre);
                    $resp['id_centro']=$centroEntity->id_centro;
                    $resp['nombre_centro']=$centroEntity->nombre_centro;
                    $resp['contratante']='--';
                    $resp['tel_contratante']='--';
                    $resp['tipo']='--';
                    $resp['adendas']=0;
                    $resp['presupuesto']= 0;
                    $resp['porcentaje_avance']='--';
                    $resp['porcentaje_teorico']='--';
                    $resp['gastado']=0;
                    $resp['anticipos_por_amortizar']=0;
                    $resp['pagado']=0;
                    $resp['facturado']=0;
                    $resp['cobrado']=0;
                    $resp['ultima_actualizacion']='';
                    $resp['nivel']=$centroEntity->nivel;
                    $resp['id_padre']=$centroEntity->id_padre;
                    $resp['isleaf']= false;

                    if($centroEntity->nivel == 0){
                        $nivel0[] = $index;
                    }
                    
                    $resp['nombre']='--';

                    $index++;
                    $items[] = $resp;
                }
                $desde = Carbon::createFromFormat('m/d/Y', '12/12/0000')->format('Y/m/d');
                $hasta = Carbon::createFromFormat('m/d/Y', '01/01/2100')->format('Y/m/d');
                //Calculos para los montos totalizadores de la tabla
                $adendas = DB::table('items_detalle')
                    ->join('items', 'items.id', '=', 'items_detalle.id_item')
                    ->join('ajustes', 'ajustes.id', '=', 'items.id_ajuste')
                    ->join('adendas', 'adendas.id', '=', 'ajustes.id_adenda')
                    ->where('adendas.id_centro', '=', $centro->id_centro)
                    ->where('adendas.estado', '=', 'aprobado')
                    ->sum('monto_nuevo');
                
                $presupuesto = DB::table('presupuesto_y_avance')
                    ->where("id_centro_contable",$centro->id_centro)
                    ->sum('presupuesto');
                $porcentaje_avance = DB::table('presupuesto_y_avance')
                    ->join('cuentas', 'cuentas.id_cuenta', '=','presupuesto_y_avance.id_cuenta')
                    ->where('cuentas.nivel','=',2)
                    ->where('presupuesto_y_avance.id_centro_contable','=',$centro->id_centro)
                    ->avg('presupuesto_y_avance.porcentaje_avance');
                $porcentaje_teorico = DB::table('presupuesto_y_avance')
                    ->join('cuentas', 'cuentas.id_cuenta', '=','presupuesto_y_avance.id_cuenta')
                    ->where('cuentas.nivel','=',2)
                    ->where('presupuesto_y_avance.id_centro_contable','=',$centro->id_centro)
                    ->avg('presupuesto_y_avance.porcentaje_teorico');                
                $gastado = Factura::where('id_centro', $centro->id_centro)
                    ->join('cuentas as c','facturas.id_cuenta','=','c.id_cuenta')
                    // ->where('c.nivel','=',2)
                    ->whereDate('fecha_transaccion','>=', $desde)
                    ->whereDate('fecha_transaccion','<=', $hasta)
                    ->sum('monto');
                $planilla = Planilla::where('id_centro', $centro->id_centro)
                    ->join('cuentas as c','planilla.id_cuenta','=','c.id_cuenta')
                    // ->where('c.nivel','=',2)
                    ->whereDate('fecha_transaccion','>=', $desde)
                    ->whereDate('fecha_transaccion','<=', $hasta)
                    ->sum('monto');
                $comp = OrdenCompra::where('id_centro', $centro->id_centro)
                    ->sum('monto_compra');
                

                $anticipos_por_amortizar = Anticipo::where('id_centro', $centro->id_centro)->sum('por_amortizar');
                $pagado = Pago::where('id_centro', $centro->id_centro)->sum('monto');
                $facturado = FacturaVenta::where('id_centro', $centro->id_centro)->sum('monto');
                $cobrado = Cobro::where('id_centro', $centro->id_centro)->sum('monto');
                
                
                $resp['id_centro']=$centro->id_centro;
                $resp['nombre_centro']=$centro->nombre_centro;
                $resp['contratante']=$centro->contratante;
                $resp['tel_contratante']=$centro->tel_contratante;
                $resp['tipo']=CentroContable::find($centro->id_centro)->tipoProyecto->nombre;
                $resp['adendas']=0+$adendas;
                $resp['presupuesto']= $presupuesto;
                $resp['porcentaje_avance']=$porcentaje_avance;
                $resp['porcentaje_teorico']=$porcentaje_teorico;
                $resp['gastado']=$gastado+$planilla;
                $resp['anticipos_por_amortizar']=0+$anticipos_por_amortizar; 
                $resp['pagado']=0+$pagado;
                $resp['facturado']=0+$facturado;
                $resp['comp']=0+$comp;
                $resp['cobrado']=0+$cobrado;    
                $resp['ultima_actualizacion']= $ultimaActualizacion;
                $resp['nivel']=$centro->nivel;
                $resp['id_padre']=$centro->id_padre;
                $resp['isleaf']= true;
                $resp['estado']=CentroContable::find($centro->id_centro)->estado->nombre;

                
                $resp['nombre']=$centro->nombre_proyecto;

                $docscentro = CentroContable::find($centro->id_centro)->documentos;
                $docstipo = CentroContable::find($centro->id_centro)->tipoproyecto->documentos;
         


                $resp['documentacion']=$docscentro->count()."/".$docstipo->count();


                $index++;
                $items[] = $resp;
            }
        }
 
        foreach($nivel0 as $i){
            $countlevel1=0;
            if((strcmp($items[$i]['nivel'],'0')==0)&&($items[$i]['isleaf']==false)){
                for ($k=0; $k<count($items); $k++) {
                    if((($items[$i]['id_centro']==$items[$k]['id_padre']))){
                        $countlevel1++;
                        $items[$i]['adendas']+= $items[$k]['adendas'];
                        $items[$i]['presupuesto']+= $items[$k]['presupuesto'];
                        $items[$i]['porcentaje_avance'] = floatval($items[$i]['porcentaje_avance'])+ floatval($items[$k]['porcentaje_avance']);
                        $items[$i]['porcentaje_teorico']= floatval($items[$i]['porcentaje_teorico']) + floatval ($items[$k]['porcentaje_teorico']);
                        $items[$i]['gastado']+=$items[$k]['gastado'];
                        $items[$i]['anticipos_por_amortizar']+= $items[$k]['anticipos_por_amortizar'];
                        $items[$i]['pagado']+=$items[$k]['pagado'];
                        $items[$i]['facturado']+= $items[$k]['facturado'];
                        $items[$i]['cobrado']+=$items[$k]['cobrado'];
                        $items[$i]['comp']+=$items[$k]['comp'];

                    }
                }
                if($countlevel1>0){

                    $items[$i]['porcentaje_avance']=$items[$i]['porcentaje_avance']/$countlevel1;
                    $items[$i]['porcentaje_teorico']=$items[$i]['porcentaje_teorico']/$countlevel1;
                }
                else{

                    $items[$i]['porcentaje_avance']=0;
                    $items[$i]['porcentaje_teorico']=0;
                }
            }
        }

        $config = ConfiguracionFormula::where('slug','presupuestoxgastar')->first();
        if($request->ajax()){
            return response()->json([
                'centros' => $items,
                'anticipopagos'=>$anticipopagos,
                'estadop'=>$estadop,
                'docp'=>$docp,
                'presupuestoxgastar'=>$config->id_valor,
            ]);
        }
    }
    public function getPorcentajeAvanceByProyecto($idCentro){
        $porcentaje_avance = DB::table('presupuesto_y_avance')
            ->join('cuentas', 'cuentas.id_cuenta', '=','presupuesto_y_avance.id_cuenta')
            ->where('cuentas.nivel','=',2)
            ->where('presupuesto_y_avance.id_centro_contable','=',$idCentro)
            ->avg('presupuesto_y_avance.porcentaje_avance');
        return $porcentaje_avance;
    }
    public function getPorcentajeTeoricoByProyecto($idCentro){
        $porcentaje_teorico = DB::table('presupuesto_y_avance')
            ->join('cuentas', 'cuentas.id_cuenta', '=','presupuesto_y_avance.id_cuenta')
            ->where('cuentas.nivel','=',2)
            ->where('presupuesto_y_avance.id_centro_contable','=',$idCentro)
            ->avg('presupuesto_y_avance.porcentaje_teorico');
        return $porcentaje_teorico;
    }
}
