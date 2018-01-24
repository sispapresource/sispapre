<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\TipoProyecto;
use App\Documento;
use App\CentroContable;

class TipoProyectoController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){

    }

    public function Create ()
    {
        return view('catalogos.tipoproyecto.create');
    }

    public function store(){
        $this->validate(request(),[
            'nombre' => 'required|min:5',
            'descripcion' => ''
        ]);

        TipoProyecto::create([
            'nombre' => request('nombre'),
            'descripcion' =>request ('descripcion')
        ]);

        return redirect('/catalogos');
    }

    public function edit(TipoProyecto $proyecto){
        return view('catalogos.tipoproyecto.editar')->with(compact('proyecto'));   
    }
    public function update(TipoProyecto $proyecto){
        $this->validate(request(),[
            'nombre' => 'required|min:5',
            'descripcion' => ''
        ]);

        $proyecto->nombre = request('nombre');
        $proyecto->descripcion = request('descripcion');
        $proyecto->save();
        return redirect('/catalogos');
    }
    public function destroy(TipoProyecto $proyecto){
        CentroContable::where('tipo',$proyecto->id)->update(['tipo'=>0]);
        $proyecto->delete();
        return redirect('/catalogos');

    }
}
