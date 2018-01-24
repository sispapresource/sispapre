<?php

namespace App\Http\Controllers;

use App\Adenda;
use App\AdendaFile;
use App\Ajuste;
use App\CentroContable;
use App\Cuenta;
use App\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Facades\Datatables;

class AdendaController extends Controller
{
    /**
     * Create a new controller adendaDataTable.
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
    public function index()
    {
        return view('adendas/list');
    }

    public function adendaDataTable(Request $request)
    {
        // $values = Adenda::with([
        //         'ajustes'=> function($query){
        //             $query->select('id','id_adenda');
        //         }
        //     ])
        //     ->get();
        
        // return [$request->textFilter];
        
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

        // return $adendas;
        return Datatables::of($adendas->get())
                ->addColumn('linkajustes', function ($adenda) {
                    $columnText = "";
                    foreach ($adenda->ajustes as $ajuste) {
                        $columnText =$columnText. "<span class=\"badge\"><a href=\"".route('ajuste.detail')."?idAjuste=".$ajuste->id."\">".($ajuste->numero==''? '--': $ajuste->numero)."</a></span>";
                    }
                    return $columnText;
                })
                ->addColumn('accion', function ($adenda) {
                    return (string) view('adendas.modal', compact('adenda'));
                })
                ->make(true);
    }
    public function create(Request $request)
    {
        $content = $request->all();
        $idCentro = $request->has('idCentro')?$content['idCentro']: '';
        $centroContable = CentroContable::find($idCentro);

        if ($request->has('idCentro')) {
            return view('adendas/create')->with(['nombre_centro'=>$centroContable->nombre_centro,'id_centro'=>$idCentro] );
        } else {
            return view('adendas/create');
        }
    }
    public function edit(Request $request)
    {
        $content = $request->all();
        $items = null;
        $idAdenda = $request->has('idAdenda')?$content['idAdenda']: '';

        $adenda = Adenda::find($idAdenda);
        $centroContable = CentroContable::find($adenda->id_centro);

        if ($request->has('idAdenda')) {
            return view('adendas/edit')->with(['descripcion'=>$adenda->descripcion,'estado'=>$adenda->estado,'numero'=>$adenda->numero, 'fecha'=>Carbon::parse($adenda->fecha)->format('m/d/Y'),'nombre_centro'=>$centroContable->nombre_centro,'id_adenda'=>$idAdenda] );
        } else {
            return view('home');
        }
    }
    public function save(Request $request)
    {
        $content = $request->all();
        $resp = 'not created';

        $descripcion = isset($content['descripcion'])?$content['descripcion']: '';
        $nro_adenda = isset($content['nro_adenda'])?$content['nro_adenda']: '';
        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';
        $fecha = isset($content['fecha'])?$content['fecha']: '';
        $estado = isset($content['estado'])?$content['estado']: '';
        
        $ade = new Adenda;
        $ade->id_centro = $idCentro;
        $ade->id_usuario = auth()->id();
        $ade->descripcion = $descripcion;
        $ade->numero = $nro_adenda;
        $ade->fecha = Carbon::parse($fecha);
        $ade->estado = $estado;

        if (!$ade->save()) {
            App::abort(500, 'Error');
        } else {
            $resp = 'success';
        }

        return response()->json([
            'response' => $resp
        ]);
    }
    public function getAdministracionByAdenda($idAdenda)
    {
        return Ajuste::where('id_adenda', $idAdenda)->sum('administracion');
    }
    public function getCostoByAjuste($idAjuste)
    {
        return Item::where("id_ajuste", $idAjuste)->sum('monto');
    }
    public function getCostoByAdenda($idAdenda)
    {
        $adendas = DB::table('items')->join('ajustes', 'items.id_ajuste', '=', 'ajustes.id')->where('ajustes.id_adenda', '=', $idAdenda)->sum('monto');

        return $adendas;
    }
    public function getItbmsByAdenda($idAdenda)
    {
        return Ajuste::where('id_adenda', $idAdenda)->sum('itbms');
    }
    public function getMontoAdendasByProyecto($idCentro)
    {
        $adendas = DB::table('items_detalle')
        ->join('items', 'items.id', '=', 'items_detalle.id_item')
        ->join('ajustes', 'ajustes.id', '=', 'items.id_ajuste')
        ->join('adendas', 'adendas.id', '=', 'ajustes.id_adenda')
        ->where('adendas.id_centro', '=', $idCentro)
        ->where('adendas.estado', '=', 'aprobado')
        ->sum('monto_nuevo');

        return $adendas;
    }
    public function getMontoAdendasByProyectoAndCuenta($idCentro, $idCuenta)
    {
        $querya = DB::table('items_detalle')
        ->join('items', 'items.id', '=', 'items_detalle.id_item')
        ->join('ajustes', 'ajustes.id', '=', 'items.id_ajuste')
        ->join('adendas', 'adendas.id', '=', 'ajustes.id_adenda')
        ->where('items_detalle.id_cuenta', '=', $idCuenta)
        ->where('adendas.estado', '=', 'aprobado')
        ->where('adendas.id_centro', '=', $idCentro);

        $adendas=$querya->sum('monto_nuevo')/*-$querya->sum('monto_anterior')*/;
        return $adendas;
    }
    public function getUtilidadByAdenda($idAdenda)
    {
        return Ajuste::where('id_adenda', $idAdenda)->sum('utilidad');
    }

    public function getDetail(Request $request)
    {
        $content = $request->all();
        $idAdenda = $request->has('idAdenda')?$content['idAdenda']: '';

        $adenda = Adenda::find($idAdenda);
        $centroContable = CentroContable::find($adenda->id_centro);

        $documents = AdendaFile::where('id_adenda', $idAdenda)->get();
        
        return view('ajustes/list')->with(['documents'=>$documents,'id_adenda'=>$idAdenda, 'nombre_centro'=>$centroContable->nombre_centro, 'numero_adenda'=>$adenda->numero]);
    }

    public function getEstadobyShort($estado)
    {
        if ($estado=='aprobado') {
            return 'Aprobado';
        }
        if ($estado=='ajuste') {
            return 'Falta Ajuste';
        }
        if ($estado=='revision') {
            return 'Por Revisar';
        }
        if ($estado=='corregir') {
            return 'Por Corregir';
        }
        if ($estado=='pendiente') {
            return 'Pendiente';
        }
        if ($estado=='aprobar') {
            return 'Por Aprobar';
        }
        if ($estado=='todos') {
            return 'Todos';
        }
    }

    public function getList(Request $request)
    {

        $content = $request->all();

        $dateFilter = isset($content['dateFilter'])?$content['dateFilter']: '';
        $estadoFilter = isset($content['estadoFilter'])?$content['estadoFilter']: '';
        $textFilter = isset($content['textFilter'])?$content['textFilter']: '';
        $numeroFilter = isset($content['numeroFilter'])?$content['numeroFilter']: '';

        if (isset($textFilter) && $textFilter!= '') {
            $adendas = DB::table('adendas')
            ->join('centros_contables', 'adendas.id_centro', '=', 'centros_contables.id_centro')
            ->where('centros_contables.nombre_centro', 'LIKE', '%'.$textFilter.'%')
            ->orderBy('adendas.fecha', 'desc')->get();
        } else {
            $adendas = Adenda::orderBy('fecha', 'desc')->get();
        }

        $items = null;

        foreach ($adendas as $adenda) {
            $addTrue = true;
            if (isset($dateFilter)) {
                $addTrue = false;
                $date_init = strtotime($dateFilter);
                $ua_comp = strtotime(isset($adenda->fecha)? $adenda->fecha:'1999-01-01 00:00:00');
                if ($ua_comp > $date_init) {
                    $addTrue = true;
                }
            }
            if (isset($numeroFilter) && $numeroFilter!= '') {
                if (stripos($adenda->numero, $numeroFilter)===false) {
                    $addTrue = false;
                }
            }
            if (isset($estadoFilter) && $estadoFilter!= '' && $estadoFilter!= 'todos') {
                if (strrpos($adenda->estado, $estadoFilter)===false) {
                    $addTrue = false;
                }
            }
            if ($addTrue) {
                $centroEntity = CentroContable::find($adenda->id_centro);
                $resp['id_adenda']=$adenda->id;
                $resp['proyecto']=$centroEntity->nombre_centro;
                $resp['fecha_documento']=$adenda->fecha;
                $resp['nro_adenda']=$adenda->numero;
                $resp['monto']= app('App\Http\Controllers\AdendaController')->getCostoByAdenda($adenda->id);
                $resp['utilidad'] = app('App\Http\Controllers\AdendaController')->getUtilidadByAdenda($adenda->id);
                $resp['admin']= app('App\Http\Controllers\AdendaController')->getAdministracionByAdenda($adenda->id);
                $resp['subtotal']= $resp['monto'] + $resp['utilidad'] + $resp['admin'];
                $resp['itbms']= app('App\Http\Controllers\AdendaController')->getItbmsByAdenda($adenda->id);
                $resp['total']= $resp['subtotal'] + $resp['itbms'];
                $resp['descripcion']=$adenda->descripcion;
                $resp['estado']=app('App\Http\Controllers\AdendaController')->getEstadobyShort($adenda->estado);
                $items[] = $resp;
            }
        }

        if ($request->ajax()) {
            return response()->json(['adendas' => $items]);
        }
    }
    public function update(Request $request)
    {
        $content = $request->all();
        $resp = 'not updated';

        $idAdenda = isset($content['idAdenda'])?$content['idAdenda']: '';
        $descripcion = isset($content['descripcion'])?$content['descripcion']: '';
        $nro_adenda = isset($content['nro_adenda'])?$content['nro_adenda']: '';
        $fecha = isset($content['fecha'])?$content['fecha']: '';
        $estado = isset($content['estado'])?$content['estado']: '';

        $paUpdate = Adenda::find($idAdenda);
        $paUpdate->descripcion = $descripcion;
        $paUpdate->numero = $nro_adenda;
        $paUpdate->estado = $estado;
        $paUpdate->fecha = Carbon::parse($fecha);

        if (!$paUpdate->save()) {
            App::abort(500, 'Error');
        } else {
            $resp = 'success';
        }
        return response()->json([
            'response' => $resp
        ]);
    }
    public function uploadDocument(Request $request)
    {

        if ($request->hasFile('document')) {
            $destinationPath = base_path() . '/public/documents/';
            $uri= url('/').'/documents/';

            try {
                $fileName = Input::file('document')->getClientOriginalName();
                Input::file('document')->move($destinationPath, $fileName);
                $uriName= $uri.$fileName;

                $adenda_file = new AdendaFile();
                $adenda_file->url = $uriName;
                $adenda_file->name_file = $fileName;
                $adenda_file->id_adenda = $request->idAdenda;

                if (Auth::check()) {
                    $adenda_file->name_user = Auth::user()->name;
                }

                $adenda_file->save();
            } catch (\Exception $e) {
                return "Error subiendo el archivo!";
            }
            return redirect()->action('AdendaController@index', ['idCentro' => $request->idCentro]);
        }
    }
}
