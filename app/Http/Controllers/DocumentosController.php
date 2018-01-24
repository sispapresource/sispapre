<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Documento;
use App\TipoProyecto;
use App\CentroContable;
use App\DocumentoProyecto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB; 
class DocumentosController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){


        $centro = CentroContable::find(request('idCentro'));
        $documentospivot = $centro->documentos();

        if($request->has('inicio')){
            $fechainicio = Carbon::createFromFormat('m/d/Y',$request->inicio)->format('Y-m-d');
            $documentospivot = $documentospivot->wherePivot('fecha_de_carga','>=',$fechainicio);
        }
        if($request->has('fin')){
            $fechafin = Carbon::createFromFormat('m/d/Y',$request->fin)->addDay()->format('Y-m-d');
            $documentospivot = $documentospivot->wherePivot('fecha_de_carga','<=',$fechafin);
        }
        //Documentos del tipo de proyecto
        $documentos = $centro->tipoProyecto->documentos()
            ->whereNotIn('id',$centro->documentos()->pluck('id'));

        if($request->has('nombre')) {
            $documentos = $documentos->where('nombre','like','%'. $request->nombre .'%');
            $documentospivot =$documentospivot->where('nombre','like','%'. $request->nombre .'%');
        }

        //dd($documentospivot);
        //Unimos los documentos cargados y los por cargar

        $documentos = $documentos->get()->merge($documentospivot->get());
        return view('documentos.documentos')->with(compact('centro','documentos'));

    }

    public function create(Request $request){
        $idCentro = null;
        if($request->has('idCentro')){
            $idCentro = $request->idCentro;
        }
        return view('documentos.create')->with(compact('idCentro'));
    }

    public function store(Request $request){

        $this->validate(request(),[
            'nombre' => 'required|min:5'
        ]);

        $requerido = false;
        if(null!==$request->requerido)
            if($request->requerido=='on')
                $requerido = true;

        $documento = Documento::create([
            'nombre' => request('nombre'),
            'requerido'=> $requerido
        ]);

        if($request->has('idCentro')){
            $idCentro = $request->idCentro;
            DocumentoProyecto::updateOrCreate([
                'documento_id'=>$documento->id,
                'centro_contable_id'=>$idCentro,
            ]);
            return redirect('/documentos?idCentro='.$idCentro);    
        }

        return redirect('/catalogos');


    }

    public function edit(Documento $documentos){
        $tiposproyecto = TipoProyecto::all();
        /*return (string) $documento->tipoproyecto->contains($tiposproyecto->find(2));*/
        return view('documentos.edit')->with(compact('documentos','tiposproyecto'));
    }

    public function update(Request $request, Documento $documentos){
        $documentos->tipoproyecto()->sync($request->tipos);
        return redirect('/catalogos');
    }

    ////

    public function cargar(Documento $documento, CentroContable $centrocontable){

        return view('documentos.cargar')->with(compact('documento','centrocontable'));
    }

    public function guardararchivo(Documento $documento, CentroContable $centrocontable){

        $this->validate(request(),[
            'document' => 'required',
            'estado'=>'required'
        ]);

        //        $nombrearchivo = request()->file('document')->getClientOriginalExtension();
        $nombrearchivo = request()->file('document')->getClientOriginalName();
        $url ='/public/documents/' .$centrocontable->nombre_centro ."/";
        $nombrecompleto=$documento->id . "_" . $centrocontable->id_centro . "_". $documento->nombre ."_".$nombrearchivo;


        $proyecto = CentroContable::find($centrocontable->id_centro);
        $documento = Documento::find($documento->id);

        if($proyecto->documentos->contains($documento)){
            $proyecto->documentos()->detach($documento);
        }

        $fecha = request('fecha');
        if(request('fecha')===""){
            $fecha=Carbon::now();
        }
        DocumentoProyecto::updateOrCreate([
            'documento_id'=>$documento->id,
            'user_id'=>Auth::id(),
            'centro_contable_id'=>$centrocontable->id_centro,
            'url' => $nombrearchivo,
            'fecha_de_carga'=> Carbon::now(),
            'fecha_de_expiracion'=> $fecha,
            'estado'=>request('estado')
        ]);

        request()->file('document')->move(
            base_path() . $url, $nombrecompleto
        );
        return redirect("/documentos?idCentro=".$centrocontable->id_centro);
    }

    public function descargar(Documento $documento, CentroContable $centrocontable){

        $nombrearchivo = CentroContable::find($centrocontable->id_centro)->documentos()->where('documento_id',$documento->id)->first()->pivot->url;
        $nombrecompleto=$documento->id . "_" . $centrocontable->id_centro . "_". $documento->nombre ."_".$nombrearchivo;

        $url = base_path() . '/public/documents/' .$centrocontable->nombre_centro ."/".$nombrecompleto;


        return response()->download($url);
    }

    public function mostrarestado(Documento $documento, CentroContable $centrocontable){

        return view('documentos.estado')->with(compact('documento','centrocontable'));
    }

    public function guardarestado(Documento $documento, CentroContable $centrocontable){
        $pivot = $centrocontable->documentos()->where('documento_id',$documento->id)->get()->first();
        $pivot->pivot->estado = request('estado');
        $pivot->pivot->save();

        return redirect("/documentos?idCentro=".$centrocontable->id_centro);


    }


}
