<?php

namespace App\Http\Controllers;

use App\CentroContable;
use App\Hallazgo;
use App\HallazgoFile;
use App\Inspeccion;
use App\InspeccionFile;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class SeguridadController extends Controller
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
        $centros = CentroContable::where('estado_id',1);
        if($request->has('nombre')){
            $centros = $centros->where('nombre_centro','like','%'.$request->nombre.'%');
        }

        $centros = $centros->paginate(10);

        return view('inspecciones.seguridad')->with(compact('centros'));
    }

    public function getList(Request $request){
        $content = $request->all();
        $textFilter = isset($content['textFilter'])?$content['textFilter']: '';//filtro de texto usado abajo
        $inspecciones = DB::table('inspecciones as latest') //
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('inspecciones')
                    ->whereRaw('id_centro = latest.id_centro')
                    ->whereRaw('fecha > latest.fecha');
            })
            ->get();

        $items = null;
        foreach ($inspecciones as $inspeccion){
            $addTrue = true;
            $centroEntity = CentroContable::find($inspeccion->id_centro);
            if(isset($textFilter) && $textFilter!= ''){ //se filtra centro contable
                if(stripos($centroEntity->nombre_centro,$textFilter)===false)
                    $addTrue = false;
            }
            if($addTrue){
                $resp['id_centro']=$inspeccion->id_centro;
                $resp['nombre_centro']=$centroEntity->nombre_centro;
                $resp['encargado']=$inspeccion->encargado;
                $resp['puntaje']=$inspeccion->puntaje;
                $resp['fecha']=$inspeccion->fecha;
                $resp['hallazgos'] = Hallazgo::where('id_centro','=',$inspeccion->id_centro)->count();;
                $items[] = $resp;
            }
        }

        if($request->ajax()){
            return response()->json(['inspecciones' => $items]);
        }
    }

    public function getHomeInspecciones(Request $request){
        $content = $request->all();
        $idCentro = $request->has('idCentro')?$content['idCentro']: '';
        $centroContable = CentroContable::find($idCentro);
        return view('inspecciones/list')->with(['id_centro'=>$idCentro,'nombre_centro'=>$centroContable->nombre_centro]);
    }
    public function getInspecciones(Request $request){
        $content = $request->all();
        $idCentro = $request->has('idCentro')?$content['idCentro']: '';

        $inspecciones = DB::table('inspecciones')->where('id_centro','=',$idCentro)
            ->get();

        $items = null;

        foreach ($inspecciones as $inspeccion){

            $document = InspeccionFile::where('id_inspeccion',$inspeccion->id)->first();

            $centroEntity = CentroContable::find($inspeccion->id_centro);
            $resp['id_inspeccion']=$inspeccion->id;
            $resp['numero_inspeccion']=$inspeccion->numero;
            $resp['encargado']=$inspeccion->encargado;
            $resp['puntaje']=$inspeccion->puntaje;
            $resp['fecha']=$inspeccion->fecha;
            if(isset($document))
                $resp['documento'] = '<a href="'.$document->url.'" target="_blank">'.$document->name_file.'</a>';
            else
                $resp['documento'] = '';
            $resp['estado']=$inspeccion->estado;
            $items[] = $resp;
        }

        if($request->ajax()){
            return response()->json(['inspecciones' => $items]);
        }
    }
    public function setEstadoInspeccion(Request $request){
        $content = $request->all();
        $idInspeccion = $request->has('idInspeccion')?$content['idInspeccion']: '';
        $newEstado = $request->has('newEstado')?$content['newEstado']: '';
        $idCentro = $request->has('idCentro')?$content['idCentro']: '';
        $centroContable = CentroContable::find($idCentro);

        $paUpdate = Inspeccion::find($idInspeccion);
        $paUpdate->estado = $newEstado;
        $paUpdate->save();

        return view('inspecciones/list')->with(['id_centro'=>$idCentro,'nombre_centro'=>$centroContable->nombre_centro]);
    }
    public function downloadInspeccion(Request $request){
        $content = $request->all();
        $idInspeccion = $request->has('idInspeccion')?$content['idInspeccion']: '';
        $document = InspeccionFile::where('id_inspeccion',$idInspeccion)->first();
        if(isset($document))
            return response()->download('./documents/'.$document->name_file.''); 
        else
            return back();             
    }
    public function getHomeHallazgos(Request $request){
        $content = $request->all();
        $idCentro = $request->has('idCentro')?$content['idCentro']: '';
        $centroContable = CentroContable::find($idCentro);
        return view('hallazgos/list')->with(['id_centro'=>$idCentro,'nombre_centro'=>$centroContable->nombre_centro]);
    }
    public function getHallazgos(Request $request){
        $content = $request->all();
        $idCentro = $request->has('idCentro')?$content['idCentro']: '';

        $hallazgos = DB::table('hallazgos')->where('id_centro','=',$idCentro)->get();

        $items = null;

        foreach ($hallazgos as $hallazgo){

            $document = HallazgoFile::where('id_hallazgo',$hallazgo->id)->first();

            $centroEntity = CentroContable::find($hallazgo->id_centro);
            $resp['id_hallazgo']=$hallazgo->id;
            $resp['numero_hallazgo']=$hallazgo->numero;
            $resp['encargado']=$hallazgo->encargado;
            $resp['referencia']=$hallazgo->referencia;
            $resp['fecha']=$hallazgo->fecha;
            if(isset($document))
                $resp['documento'] = '<a href="'.$document->url.'" target="_blank">'.$document->name_file.'</a>';
            else
                $resp['documento'] = '';
            $resp['estado']=$hallazgo->estado;
            $items[] = $resp;
        }

        if($request->ajax()){
            return response()->json(['hallazgos' => $items]);
        }
    }
    public function setEstadoHallazgo(Request $request){
        $content = $request->all();
        $idHallazgo = $request->has('idHallazgo')?$content['idHallazgo']: '';
        $newEstado = $request->has('newEstado')?$content['newEstado']: '';
        $idCentro = $request->has('idCentro')?$content['idCentro']: '';
        $centroContable = CentroContable::find($idCentro);

        $paUpdate = Hallazgo::find($idHallazgo);
        $paUpdate->estado = $newEstado;
        $paUpdate->save();

        return view('hallazgos/list')->with(['id_centro'=>$idCentro,'nombre_centro'=>$centroContable->nombre_centro]);
    }
    public function createHallazgo(Request $request){
        $content = $request->all();
        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';

        $centroContable = CentroContable::find($idCentro);

        if(isset($centroContable)){
            $ultimoRegistro = DB::table('hallazgos')->max('id')+1;
            if(strlen($ultimoRegistro)<1)
                $ultimoRegistro = 1;
            return view('hallazgos/create')->with(['ultimo_registro'=>$ultimoRegistro,'nombre_centro'=>$centroContable->nombre_centro, 'id_centro'=>$idCentro] );
        }        
        else
            return view('home');   
    }
    public function saveHallazgo(Request $request){

        $hal = new Hallazgo;
        $hal->id_centro = $request->idCentro;
        $hal->referencia = $request->referencia_hallazgo;
        $hal->numero = $request->nro_hallazgo;
        $hal->fecha = Carbon::parse($request->date_hallazgo);
        $hal->encargado = $request->encargado_hallazgo;
        $hal->estado = 'subsanar';
        $hal->save();

        if($request->hasFile('document')){

            $destinationPath = base_path() . '/public/documents/';
            $uri= url('/').'/documents/';

            $fileName = Input::file('document')->getClientOriginalName();
            Input::file('document')->move($destinationPath, $fileName);
            $uriName= $uri.$fileName;

            $hallazgo_file = new HallazgoFile();
            $hallazgo_file->url = $uriName;
            $hallazgo_file->name_file = $fileName;
            $hallazgo_file->id_hallazgo = $hal->id;

            if (Auth::check())
                $hallazgo_file->name_user = Auth::user()->name;

            $hallazgo_file->save();
        }
        return redirect()->action('SeguridadController@getHomeHallazgos', ['idCentro' => $request->idCentro]);      
    }
    public function downloadHallazgo(Request $request){
        $content = $request->all();
        $idHallazgo = $request->has('idHallazgo')?$content['idHallazgo']: '';
        $document = HallazgoFile::where('id_hallazgo',$idHallazgo)->first();
        if(isset($document))
            return response()->download('./documents/'.$document->name_file.''); 
        else
            return back();             
    }

    public function createInspeccion(Request $request){
        if($request->has('idCentro'))
        {
            $centro = CentroContable::find($request->idCentro);
            return view('inspecciones.create')->with(compact('centro'));
        }

    }
    public function storeInspeccion(CentroContable $centrocontable, Request $request){
        $this->validate($request,[
            'numero' => 'required',
            'referencia' => 'required',
            'encargado' => 'required',
            'fecha' => 'required',
            'documento' => 'required',
            'puntaje' => 'required'
        ]);

        $inspeccion = Inspeccion::create([
            'id_centro'=>$centrocontable->id_centro,
            'encargado'=>$request->encargado,
            'puntaje'=>$request->puntaje,
            'fecha'=>Carbon::parse($request->fecha),
            'numero'=>$request->numero,
            'estado'=>"revision"
        ]);

        if($request->hasFile('documento')){

            $destinationPath = base_path() . '/public/documents/';
            $uri= url('/').'/documents/';

            $fileName = Input::file('documento')->getClientOriginalName();
            Input::file('documento')->move($destinationPath, $fileName);
            $uriName= $uri.$fileName;

            $inspeccion_file = new InspeccionFile();
            $inspeccion_file->url = $uriName;
            $inspeccion_file->name_file = $fileName;
            $inspeccion_file->id_inspeccion = $inspeccion->id;

            if (Auth::check())
                $inspeccion_file->name_user = Auth::user()->name;

            $inspeccion_file->save();
        }

        return redirect()->action('SeguridadController@getHomeInspecciones', ['idCentro' =>$centrocontable->id_centro]);      
    }

}
