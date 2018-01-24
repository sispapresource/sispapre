<?php

namespace App\Http\Controllers;

use App\Adenda;
use App\Ajuste;
use App\CentroContable;
use App\Item;
use App\ItemDetalle;
use App\ItemFile; 
use App\LogCambios;
use App\Cuenta;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\PresupuestoAvance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ItemController extends Controller
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
    public function create(Request $request){
        $content = $request->all();
        $idAjuste = isset($content['idAjuste'])?$content['idAjuste']: '';

        $ajuste = Ajuste::find($idAjuste);

        if(isset($ajuste)){
            $adenda = Adenda::find($ajuste->id_adenda);
            $centroContable = CentroContable::find($adenda->id_centro);
            return view('items/create')->with(['nombre_centro'=>$centroContable->nombre_centro,'id_ajuste'=>$idAjuste,'nro_adenda'=>$adenda->numero, 'nro_ajuste'=>$ajuste->numero, 'id_centro'=>$adenda->id_centro] );
        }        
        else
            return view('home');   
    }
    public function getDetail(Request $request)
    {
        $content = $request->all();
        $items = null;
        $idItem = isset($content['idItem'])?$content['idItem']: '';

        $item = Item::find($idItem);

        if(isset($item)){

            $ajusteEntity = Ajuste::find($item->id_ajuste);
            $adenda = Adenda::find($ajusteEntity->id_adenda);
            $centroEntity = CentroContable::find($adenda->id_centro);
            $UserEntity = User::find($item->id_usuario);

            $query = DB::table('items_detalle')->where("id_item",$idItem);
            $query = $query->get();

            foreach ($query as $items_detalle){

                $cuentaEntity = Cuenta::find($items_detalle->id_cuenta);

                $resp['nombre_cuenta']= $items_detalle->id_cuenta.' '.$cuentaEntity->nombre_cuenta;
                $resp['monto_actual']=$items_detalle->monto_anterior;
                $resp['monto_modificado']=$items_detalle->monto_nuevo;

                $items[] = $resp;
            }
            return view('items/detail')->with(['numero_item'=>$item->numero, 'observaciones'=>$item->observaciones,'nombre_centro'=>$centroEntity->nombre_centro,'numero_adenda'=>$adenda->numero,'numero_ajuste'=>$ajusteEntity->numero, 'usuario'=>$UserEntity->name,'monto'=>$item->monto,'items_detalle'=>$items] );
        }else{
            return view('home');
        }   
    }
    public function getList(Request $request){

        $content = $request->all();

        $dateFilter = isset($content['dateFilter'])?$content['dateFilter']: '';
        $textFilter = isset($content['textFilter'])?$content['textFilter']: '';
        $idAjuste = isset($content['idAjuste'])?$content['idAjuste']: '';

        if(isset($textFilter) && $textFilter!= ''){ // falta hacer filtros
            $ajuste = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('centros_contables.nombre_centro','LIKE','%'.$textFilter.'%')->get();
        }else
            $items = Item::where('id_ajuste',$idAjuste)->get();

        $fields = null;

        foreach ($items as $item){
            $addTrue = true;
            if(isset($dateFilter)){
                $addTrue = false;
                $date_init = strtotime($dateFilter);
                $ua_comp = strtotime(isset($item->fecha)? $item->fecha:'1999-01-01 00:00:00');
                if($ua_comp > $date_init)
                    $addTrue = true;
            }
            if($addTrue){
                $ajusteEntity = Ajuste::find($idAjuste);
                $adendaEntity = Adenda::find($ajusteEntity->id_adenda);
                $resp['id_item']=$item->id;
                $resp['nro_documento']=$adendaEntity->numero;
                $resp['nro_ajuste']=$ajusteEntity->numero;
                $resp['nro_item']=$item->numero;
                $resp['fecha']=$ajusteEntity->fecha;
                $resp['costo']= $item->monto;
                $fields[] = $resp;
            }
        }

        if($request->ajax()){
            return response()->json([
                'fields' => $fields
            ]);
        }
    }
    public function save(Request $request){
        $content = $request->all();
        $resp = 'not created';

        $nro_item = isset($content['nro_item'])?$content['nro_item']: '';
        $idAjuste = isset($content['idAjuste'])?$content['idAjuste']: '';
        $monto = isset($content['monto'])?$content['monto']: '';
        $observaciones = isset($content['observaciones'])?$content['observaciones']: '';
        $amountData = json_decode($content['amounts'], true);
        
        if(count($amountData)>0){
            $ite = new Item;
            $ite->id_ajuste = $idAjuste;
            $ite->id_usuario = auth()->id();
            $ite->monto = $monto;
            $ite->numero = $nro_item;
            $ite->observaciones = $observaciones;

            $ite->save();

            foreach($amountData as $item) { 
                $item['id_item']=$ite->id;
                ItemDetalle::create($item);
            }
            $resp = 'success';
        }
        return response()->json([
            'response' => $resp
        ]);     
    }
}
