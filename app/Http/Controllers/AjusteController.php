<?php

namespace App\Http\Controllers;

use App\Adenda;
use App\Ajuste;
use App\CentroContable;
use App\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class AjusteController extends Controller
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
        return view('ajustes/list');
    }
    public function create(Request $request){
        $content = $request->all();
        $idAdenda = isset($content['idAdenda'])?$content['idAdenda']: '';

        $adenda = Adenda::find($idAdenda);

        if(isset($adenda)){
            $centroContable = CentroContable::find($adenda->id_centro);
            $ultimoRegistro = DB::table('ajustes')->max('id')+1;
            if(strlen($ultimoRegistro)<1)
                $ultimoRegistro = 1;
            return view('ajustes/create')->with(['ultimo_registro'=>$ultimoRegistro,'nombre_centro'=>$centroContable->nombre_centro,'id_adenda'=>$idAdenda,'nro_adenda'=>$adenda->numero] );
        }        
        else
            return view('home');   
    }
    public function edit(Request $request){
        $content = $request->all();
        $items = null;
        $idAjuste = $request->has('idAjuste')?$content['idAjuste']: '';

        $ajuste = Ajuste::find($idAjuste);
        $adenda = Adenda::find($ajuste->id_adenda);
        $centroContable = CentroContable::find($adenda->id_centro);

        if($request->has('idAjuste')){
            return view('ajustes/edit')->with(['nro_ajuste'=>$ajuste->numero,'descripcion'=>$ajuste->descripcion,'utilidad'=>$ajuste->utilidad,'administracion'=>$ajuste->administracion,'itbms'=>$ajuste->itbms,'nro_adenda'=>$adenda->numero, 'fecha'=>Carbon::parse($ajuste->fecha)->format('m/d/Y'),'nombre_centro'=>$centroContable->nombre_centro,'id_ajuste'=>$idAjuste] );
        }else
            return view('home');   
    }
    public function getDetail(Request $request){
        $content = $request->all();
        $idAjuste = $request->has('idAjuste')?$content['idAjuste']: '';

        $ajuste = Ajuste::find($idAjuste);
        $adenda = Adenda::find($ajuste->id_adenda);
        $centroContable = CentroContable::find($adenda->id_centro);
        
        return view('items/list')->with(['id_ajuste'=>$idAjuste,'nombre_centro'=>$centroContable->nombre_centro, 'numero_adenda'=>$adenda->numero, 'numero_ajuste'=>$ajuste->numero]);
    }
    public function getList(Request $request){

        $content = $request->all();

        $dateFilter = isset($content['dateFilter'])?$content['dateFilter']: '';
        $textFilter = isset($content['textFilter'])?$content['textFilter']: '';
        $idAdenda = isset($content['idAdenda'])?$content['idAdenda']: '';

        if(isset($textFilter) && $textFilter!= ''){ // falta hacer filtros
            $ajustes = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('centros_contables.nombre_centro','LIKE','%'.$textFilter.'%')->get();
        }else
            $ajustes = Ajuste::where('id_adenda',$idAdenda)->get();

        $items = null;

        foreach ($ajustes as $ajuste){
            $addTrue = true;
            if(isset($dateFilter)){
                $addTrue = false;
                $date_init = strtotime($dateFilter);
                $ua_comp = strtotime(isset($adenda->fecha_transaccion)? $adenda->fecha_transaccion:'1999-01-01 00:00:00');
                if($ua_comp > $date_init)
                    $addTrue = true;
            }
            if($addTrue){
                $adendaEntity = Adenda::find($idAdenda);
                $resp['id_ajuste']=$ajuste->id;
                $resp['nro_documento']=$adendaEntity->numero;
                $resp['nro_ajuste']=$ajuste->numero;
                $resp['fecha']=$ajuste->fecha;
                $resp['cant_items'] = Item::where('id_ajuste','=',$ajuste->id)->count();//where('id_ajuste',$ajuste->id)->count();// valor real falta
                $resp['descripcion']=$ajuste->descripcion;
                $resp['costo']= app('App\Http\Controllers\AdendaController')->getCostoByAjuste($ajuste->id);
                $resp['utilidad']= $ajuste->utilidad;
                $resp['admin']= $ajuste->administracion;
                $resp['subtotal'] = $resp['costo'] + $resp['utilidad'] + $resp['admin'];
                $resp['itbms']= $ajuste->itbms;
                $resp['total']= $resp['subtotal'] + $resp['itbms'];
                $items[] = $resp;
            }
        }

        if($request->ajax()){
            return response()->json([
                'ajustes' => $items
            ]);
        }
    }
    public function save(Request $request){
        $content = $request->all();
        $resp = 'not created';

        $descripcion = isset($content['descripcion'])?$content['descripcion']: '';
        $nro_ajuste = isset($content['nro_ajuste'])?$content['nro_ajuste']: '';
        $idAdenda = isset($content['idAdenda'])?$content['idAdenda']: '';
        $fecha = isset($content['fecha'])?$content['fecha']: '';
        $utilidad = isset($content['utilidad'])?$content['utilidad']: '';
        $administracion = isset($content['administracion'])?$content['administracion']: '';
        $itbms = isset($content['itbms'])?$content['itbms']: '';
        
        $aju = new Ajuste;
        $aju->id_adenda = $idAdenda;
        $aju->id_usuario = auth()->id();
        $aju->descripcion = $descripcion;
        $aju->numero = $nro_ajuste;
        $aju->fecha = Carbon::parse($fecha);
        $aju->utilidad = $utilidad;
        $aju->administracion = $administracion;
        $aju->itbms = $itbms;

        if(!$aju->save())
            App::abort(500, 'Error');
        else
            $resp = 'success';

        return response()->json([
            'response' => $resp
        ]);     
    }
    public function update(Request $request){
        $content = $request->all();
        $resp = 'not updated';

        $idAjuste = isset($content['idAjuste'])?$content['idAjuste']: '';
        $descripcion = isset($content['descripcion'])?$content['descripcion']: '';
        $nro_ajuste = isset($content['nro_ajuste'])?$content['nro_ajuste']: '';
        $fecha = isset($content['fecha'])?$content['fecha']: '';
        $utilidad = isset($content['utilidad'])?$content['utilidad']: '';
        $administracion = isset($content['administracion'])?$content['administracion']: '';
        $itbms = isset($content['itbms'])?$content['itbms']: '';

        $paUpdate = Ajuste::find($idAjuste);
        $paUpdate->descripcion = $descripcion;
        $paUpdate->numero = $nro_ajuste;
        $paUpdate->utilidad = $utilidad;
        $paUpdate->administracion = $administracion;
        $paUpdate->itbms = $itbms;
        $paUpdate->fecha = Carbon::parse($fecha);

        if(!$paUpdate->save()){
            App::abort(500, 'Error');
        }else{
            $resp = 'success';
        }
        return response()->json([
            'response' => $resp
        ]);
    }
    
}
