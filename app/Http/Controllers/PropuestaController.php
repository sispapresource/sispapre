<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Propuesta;
use App\LineaDeVenta;
use Illuminate\Support\Facades\Auth;
use App\VersionPropuesta;
use App\CentroContable;

class PropuestaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $propuestas = Propuesta::paginate(10);
        return view('propuestas.index')->with(compact('propuestas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('propuestas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $this->validate(request(),[
            'No_De_Propuesta' => 'required',
            'clientes'=>'required'
        ]);

        if($request->crearlinea=="on"){

            $this->validate(request(),[
                'crear_linea' => 'required'
            ]);

            $linea = LineaDeVenta::create([ 
                'nombre' => $request->crear_linea
            ]);

            $linea = $linea->id;
        }else{
            $this->validate(request(),[
                'Linea_De_Venta' => 'required'
            ]);
            $linea = $request->Linea_De_Venta;

        }
        if($request->crearcentro=="on"){

            $this->validate(request(),[
                'Centro_Contable_create' => 'required'
            ]);
            $last = CentroContable::orderBy('id_centro','DESC')->first();

            CentroContable::create([ 
                'id_centro'=>$last->id_centro + 1,
                'nombre_centro' => $request->Centro_Contable_create
            ]);
            $centro = CentroContable::where('nombre_centro',$request->Centro_Contable_create)->first();
            
            $centro = $centro->id_centro;
        }else{
            $this->validate(request(),[
                'Centro_Contable' => 'required'
            ]);
            $centro = $request->Centro_Contable;

        }

        $propuesta = Propuesta::create([
            'id_linea_de_venta' => $linea,
            'id_centro' => $centro,
            'no_de_propuesta' => $request->No_De_Propuesta,
            'id_usuario'=>Auth::id()
        ]);

        $propuesta->clientes()->attach($request->clientes);

        return redirect(route('propuestas.index'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

        $versiones=VersionPropuesta::where('id_propuesta',$id)
            ->orderBy('created_at','ASC')
            ->paginate(10);
        $propuesta=Propuesta::find($id);

        return view('propuestas.show')->with(compact('versiones','propuesta'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
