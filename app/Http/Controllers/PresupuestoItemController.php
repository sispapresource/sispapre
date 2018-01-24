<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\CentroContable;
use App\Factura;
use App\Planilla;
use App\OrdenCompra;
use App\PresupuestoAvance;
use App\PresupuestoItem;
use App\Adenda;
use DB;

class PresupuestoItemController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        //
        $centro = CentroContable::find($request->idCentro);
        
        $adendas = Adenda::where('id_centro',$request->idCentro)
        ->join('ajustes as a','a.id_adenda','=','adendas.id')
        ->join('items as i','i.id_ajuste','=','a.id')
        ->join('items_detalle as id','id.id_item','=','i.id')
        ->join('cuentas as c','c.id_cuenta','=','id.id_cuenta')
        ->get();
        $adendas = $adendas->groupBy('categoria');
        
        
        $facturas = Factura::where('id_centro','=',$request->idCentro)
        ->join('cuentas as c','c.id_cuenta','=','facturas.id_cuenta')
        ->select('c.categoria as categoria','monto as monto','cantidad as cantidad')
        ->get();
        
        $facturas = $facturas->groupBy('categoria');
        
        $planillas = Planilla::where('id_centro','=',$request->idCentro)
        ->join('cuentas as c','c.id_cuenta','=','planilla.id_cuenta')
        ->select('c.categoria as categoria','monto as monto','cantidad_horas as cantidad')
        ->get();
        
        $planillas = $planillas->groupBy('categoria');
        
        $ordenescompra = OrdenCompra::where('id_centro','=',$request->idCentro)
        ->join('cuentas as c','c.id_cuenta','=','ordenes_de_compra.id_cuenta')
        ->select('c.categoria as categoria','monto_compra as monto','cantidad as cantidad')
        ->get();
        
        $presupuesto = PresupuestoAvance::where('id_centro_contable','=',$request->idCentro)
        ->join('cuentas as c','c.id_cuenta','=','presupuesto_y_avance.id_cuenta')
        ->select('c.categoria as categoria','presupuesto_y_avance.presupuesto as presupuesto')
        ->get();
        
        $presupuestoOriginal = PresupuestoItem::where('id_centro',$request->idCentro)
        ->with('cuentas')   
        ->get();
        
        return view('presupuestoitems.index')->with(compact(
            'centro',
            'presupuestoOriginal',
            'adendas',
            'presupuesto',
            'planillas',
            'facturas'
        ));
    }
    
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        //
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
        
        
    }
    
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show(Request $request, $id)
    {
        //
        
        $centro = CentroContable::find($request->idCentro);
                
        $facturas = Factura::where('id_centro','=',$request->idCentro)
        ->join('cuentas as c','c.id_cuenta','=','facturas.id_cuenta')
        ->where('c.categoria','=',$id)
        ->with('item')
        ->get();
        
        $planillas = Planilla::where('id_centro','=',$request->idCentro)
        ->join('cuentas as c','c.id_cuenta','=','planilla.id_cuenta')
        ->where('c.categoria','=',$id)
        ->with('item')
        ->get();
        
        $presupuestoOriginal = PresupuestoItem::where('id_centro',$request->idCentro)
        ->with('cuentas')
        ->get();
        $presupuestoOriginal = $presupuestoOriginal->where('cuentas.categoria',$id);
        return view('presupuestoitems.show')->with(compact(
            'centro',
            'presupuestoOriginal',
            'facturas',
            'planillas'
        ));
        
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
