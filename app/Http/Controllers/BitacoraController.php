<?php

namespace App\Http\Controllers;

use App\CentroContable;
use App\Cuenta;
use App\User;
use App\Http\Requests;
use App\PresupuestoAvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BitacoraController extends Controller
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
    public function index(Request $request){
        $content = $request->all();
        $items = null;
        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';

        $centroContable = CentroContable::find($idCentro);

        if(isset($idCentro)){

            $adendas = DB::table('adendas')->where('id_centro','=',$idCentro);
            $query = $adendas->get();

            foreach ($query as $item){

                $UserEntity = User::find($item->id_usuario);

                $resp['fecha_transaccion']=$item->fecha;
                $resp['usuario']= $UserEntity->name;
                $resp['id']='Adenda <a href=adenda_detail?idAdenda='.$item->id.'>'.$item->numero.'</a>';
                $items[] = $resp;
            }
            $query2 = DB::table('log_cambios')->where("id_centro",$idCentro)->where("descripcion_cambio","porcentaje");
            $query2 = $query2->get();
            foreach ($query2 as $logs){

                    $UserEntity = User::find($logs->id_usuario);

                    $cuentaEntity = Cuenta::find($logs->id_cuenta);

                    $resp['fecha_transaccion']=$logs->fecha_entrada;
                    $resp['usuario']= $UserEntity->name;     
                    $resp['id']='Actualizacion de avances de cuenta "'.$cuentaEntity->nombre_cuenta.'" de '.intval($logs->valor_anterior).'% a '.intval($logs->valor_nuevo).'%';

                    $items[] = $resp;
                }
            return view('bitacora')->with(['nombre_centro'=>$centroContable->nombre_centro,'adendas'=>$items] );
        }else
            return view('home');
    }
}