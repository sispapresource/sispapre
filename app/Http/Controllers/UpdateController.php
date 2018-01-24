<?php

namespace App\Http\Controllers;

use App\CentroContable;
use App\LogCambios;
use App\Cuenta;
use App\Http\Requests;
use App\PresupuestoAvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
class UpdateController extends Controller
{
    /**
     * Create a new controller instance.
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
        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';

        $centroContable = CentroContable::find($idCentro);

        if(isset($idCentro)){

            $query = DB::table('presupuesto_y_avance')->where("id_centro_contable",$idCentro);
            $query = $query->get();

            foreach ($query as $cuenta){

                $adendas = app('App\Http\Controllers\AdendaController')->getMontoAdendasByProyectoAndCuenta($idCentro,$cuenta->id_cuenta);

                $ultimaActualizacion = DB::table('log_cambios')->where("id_centro",$idCentro)->where("id_cuenta",$cuenta->id_cuenta)->max('fecha_entrada');

                $cuentaEntity = Cuenta::find($cuenta->id_cuenta);

                $resp['id_cuenta']=$cuenta->id_cuenta;
                $resp['nombre_cuenta']= $cuentaEntity->nombre_cuenta ;
                $resp['presupuesto']=$cuenta->presupuesto+$adendas;
                $resp['porcentaje_avance']= $cuenta->porcentaje_avance;
                $resp['teorico']= $cuenta->porcentaje_teorico;
                $resp['ultima_actualizacion']= $ultimaActualizacion;

                $items[] = $resp;
            }

            return view('update-cuentas')->with(['cuentas'=>$items,'nombre_centro'=>$centroContable->nombre_centro,'id_centro'=>$idCentro] );

        }else{
            return view('home');
        }
    }

    public function getCuentas(Request $request){

        $content = $request->all();
        $items = null;

        $cuentaFilter = isset($content['cuentaFilter'])?$content['cuentaFilter']: '';
        $codigoFilter = isset($content['codigoFilter'])?$content['codigoFilter']: '';
        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';
        $nivel = isset($content['nivel'])?$content['nivel']: '';

        if(isset($nivel) && $nivel!= ''){
            $query = DB::table('cuentas')->where("nivel",$nivel)->get();
            foreach ($query as $cuenta){
                $resp['id_cuenta']=$cuenta->id_cuenta;
                $resp['nombre_cuenta']= $cuenta->nombre_cuenta ;
                $resp['presupuesto']=0;
                
                $items[] = $resp;    
            }
        }
        else{
            if(isset($cuentaFilter) && $cuentaFilter!= ''){
                $query = DB::table('presupuesto_y_avance')
                ->join('cuentas', 'presupuesto_y_avance.id_cuenta', '=', 'cuentas.id_cuenta')
                ->where('cuentas.nombre_cuenta','LIKE','%'.$cuentaFilter.'%')
                ->where("presupuesto_y_avance.id_centro_contable",$idCentro)->get();
            }
            else
                $query = DB::table('presupuesto_y_avance')->where("id_centro_contable",$idCentro)->get();

            foreach ($query as $cuenta){

                $addTrue = true;
                $adendas = app('App\Http\Controllers\AdendaController')->getMontoAdendasByProyectoAndCuenta($idCentro,$cuenta->id_cuenta);
                $ultimaActualizacion = DB::table('log_cambios')->where("id_centro",$idCentro)->where("id_cuenta",$cuenta->id_cuenta)->max('fecha_entrada');
                $cuentaEntity = Cuenta::find($cuenta->id_cuenta);

                if(isset($codigoFilter) && $codigoFilter != ''){
                   if(stripos($cuenta->id_cuenta,$codigoFilter)===false)
                        $addTrue = false;
                }
                if($addTrue){
                    $resp['id_cuenta']=$cuenta->id_cuenta;
                    $resp['nombre_cuenta']= $cuentaEntity->nombre_cuenta ;
                    $resp['presupuesto']=$cuenta->presupuesto+$adendas;
                    $resp['porcentaje_avance']= $cuenta->porcentaje_avance;
                    $resp['teorico']= $cuenta->porcentaje_teorico;
                    $resp['ultima_actualizacion']= $ultimaActualizacion;
                    
                    $items[] = $resp;
                }
            }
        }
        if($request->ajax()){
            return response()->json(['cuentas' => $items,'teorico' => Auth::user()->can('actualizar.avanceteorico'),'fisico' => Auth::user()->can('actualizar.avancefisico')]);
        }
    }

    public function update(Request $request){
        $content = $request->all();
        $resp = 'not updated';

        $avance = isset($content['porcentaje'])?$content['porcentaje']: '';
        $teorico = isset($content['teorico'])?$content['teorico']: '';
        $id_cuenta = isset($content['id'])?$content['id']: '';
        $operacion = isset($content['oper'])?$content['oper']: '';

        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';

        if($operacion == 'edit'){

            $query = DB::table('presupuesto_y_avance')->where("id_centro_contable",$idCentro)->where("id_cuenta",$id_cuenta);
            $query = $query->get();

            foreach ($query as $cuenta){

                $paUpdate = PresupuestoAvance::find($cuenta->id);
                $porcentaje_anterior=$paUpdate->porcentaje_avance;
                $paUpdate->porcentaje_avance = $avance;
                $paUpdate->porcentaje_teorico = $teorico;

                $log = new LogCambios;
                $log->id_usuario = auth()->id();
                $log->descripcion_cambio = 'porcentaje';
                $log->id_centro = $idCentro;
                $log->id_cuenta = $id_cuenta;
                $log->valor_anterior = $porcentaje_anterior;
                $log->valor_nuevo = $avance;

                $log->save();

                if(!$paUpdate->save()){
                    App::abort(500, 'Error');
                }else{
                    $resp = 'success';
                }
            }
        }
        return response()->json([
            'response' => $resp
        ]);
    }
}
