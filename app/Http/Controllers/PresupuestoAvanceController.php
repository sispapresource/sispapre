<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\CentroContable;
use App\PresupuestoAvance;
use App\Cuenta;
use Alert;

class PresupuestoAvanceController extends Controller
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
        $centro = CentroContable::find($request->id_centro);

        $cuentas = PresupuestoAvance::where('id_centro_contable',$request->id_centro)->pluck('id_cuenta');
        $cuentas = Cuenta::where('nivel',2)->whereNotIn('id_cuenta',$cuentas)->pluck('id_cuenta','id_cuenta');

        $arbol = PresupuestoAvance::where('id_centro_contable',$request->id_centro)
            ->join('cuentas as l2','presupuesto_y_avance.id_cuenta','=','l2.id_cuenta')
            ->join('cuentas as l1','l2.id_padre','=','l1.id_cuenta')
            ->join('cuentas as l0','l1.id_padre','=','l0.id_cuenta')
            ->select('presupuesto_y_avance.id','l2.id_cuenta','l1.id_cuenta as nivel1','l0.id_cuenta as nivel0','presupuesto_y_avance.presupuesto');
        if($request->has('codigo')){
            $arbol = $arbol->where('l2.id_cuenta','like',$request->codigo.'%');
        }


        $arbol = $arbol->get();
        return view('presupuestoavance.create')->with(compact('arbol','centro','cuentas'));
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
            'presupuesto'=>'required',
            'cuenta'=>'required'
        ]);

        PresupuestoAvance::create([
            'id_cuenta'=>$request->cuenta,
            'id_centro_contable'=>$request->id_centro,
            'presupuesto'=>$request->presupuesto,
            'porcentaje_avance'=>0,
            'porcentaje_teorico'=>0
        ]);
        Alert::success('Se ha agregado con exito la cuenta ('. $request->cuenta .'), presupuesto ($ '. $request->presupuesto .' ).')->persistent("Close");
        return back();
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
        $this->validate($request,[
            'presupuesto'=>'required'
        ]);
        $presupuesto = PresupuestoAvance::find($id);
        $presupuesto->presupuesto = $request->presupuesto;
        $presupuesto->save();
        return response()->json(['response' => 'updated']);
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
