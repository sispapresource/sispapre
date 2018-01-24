<?php

namespace App\Http\Controllers;

use App\CentroContable;
use App\LogCambios;
use App\TipoProyecto;
use App\Cuenta;
use App\Http\Requests;
use App\PresupuestoAvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateProyectoController extends Controller
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

    public function index(Request $request)
    {
        $content = $request->all();
        $idCentro = $request->has('idCentro')?$content['idCentro']: '';

        $centro = CentroContable::find($idCentro);
        $tipos = TipoProyecto::all();

        if($centro){
            return view('update-proyectos')->with(compact('centro','tipos'));
        }else
            return view('home');   
    }

    public function update(Request $request){
        $content = $request->all();
        $resp = 'not updated';

        $contratante = isset($content['contratante'])?$content['contratante']: '';
        $tel_contratante = isset($content['tel_contratante'])?$content['tel_contratante']: '';
        $tipo = isset($content['tipo'])?$content['tipo']: '';
        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';
        $nombre_proyecto = isset($content['nombre_proyecto'])?$content['nombre_proyecto']: '';
        $paUpdate = CentroContable::find($idCentro);
        $paUpdate->contratante = $contratante;
        $paUpdate->tel_contratante = $tel_contratante;
        $paUpdate->tipo = $tipo;
        $paUpdate->nombre_proyecto = $nombre_proyecto;
        $paUpdate->save();

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
