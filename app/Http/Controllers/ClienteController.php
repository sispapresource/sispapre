<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Cliente;
class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $callback=false;
        if($request->has('callback'))
            if($request->callback=="1")
                $callback = $request->callback;
        return view('catalogos.clientes.create')->with(compact('callback'));
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
        $this->validate($request,[
            'nombre'=>'required|min:5',
            'direccion'=>'min:5',
            'telefono'=>'min:5',
            'nombrecontacto'=>'min:5',
            'telefonocontacto'=>'min:5'
        ]);

        $cliente = Cliente::create([
            'nombre'=>$request->nombre
        ]);
        $cliente->direccion = $request->direccion;
        $cliente->telefono = $request->telefono;
        $cliente->nombre_contacto = $request->nombrecontacto;
        $cliente->telefono_contacto = $request->telefonocontacto;
        $cliente->save();

        if($request->has('callback'))
            if($request->callback=="1")
                return redirect()->route('propuestas.create');

        return redirect('/catalogos');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Cliente $cliente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
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
    public function update(Request $request, Cliente $cliente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {
        //
    }
}
