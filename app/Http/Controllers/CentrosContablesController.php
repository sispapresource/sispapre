<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\CentroContable;

class CentrosContablesController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){

    }

    public function cambiarEstado(Request $request){
        $content = $request->all();
        $items = null;
        $resp = null;
        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';

        $centro = CentroContable::find($idCentro);

        return view('centroscontables.cambiarestado',compact('centro'));

    }
    public function guardarEstado(Request $request){
        $content = $request->all();
        $idCentro = isset($content['idCentro'])?$content['idCentro']: '';
        $estado = isset($content['idCentro'])?$content['idCentro']: '';

        $centro = CentroContable::find($idCentro);
        $centro->estado_id = $request->estados;
        $centro->save();
        return redirect('/');


    }


}
