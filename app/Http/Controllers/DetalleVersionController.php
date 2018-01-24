<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\VersionPropuesta;
use App\CategoriaPropuesta;
use App\ItemPropuesta;
use App\Propuesta;
use App\UnidadItem;
use App\DetalleVersionPropuesta;

class DetalleVersionController extends Controller
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
    public function index(VersionPropuesta $versiones)
    {
        //
        //App\CategoriaPropuesta::first()->detalles()->where('id_propuesta',1)->get()
        $allCat= CategoriaPropuesta::all();
        $categorias = array();
        $granTotal=0;
        foreach($allCat as $categoria){
            //Armando array detalles x losa;

            $detalles = array();
            $losas = $categoria->detalles()->where('id_version',$versiones->id)->groupBy('losa')->pluck('losa');
            foreach($losas as $losa){
                $detTemp = array(
                    'losa'=>$losa,
                    'detalles'=>$categoria->detalles()->where('id_version',$versiones->id)->where('losa',$losa)->get()
                );
                array_push($detalles,$detTemp);
            }

            $totaltemp = 0;
            //return $categoria->detalles()->where('id_version',$versiones->id)->first()->total();
            foreach($categoria->detalles()->where('id_version',$versiones->id)->get() as $detalle){
                $totaltemp+= $detalle->total();
            }

            $temp = array(
                'id'=>$categoria->id,
                'nombre'=>$categoria->nombre,
                'total'=>$totaltemp,
                'detalles'=>$detalles
            );
            $granTotal+=$totaltemp;
            array_push($categorias,$temp);



        }

        //        return $categorias;
        return view('detalleversion.index')->with(compact('categorias','versiones','granTotal'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, VersionPropuesta $versiones)
    {
        //
        $categoria = CategoriaPropuesta::find($request->categoria);
        $propuesta = Propuesta::find($versiones->id_propuesta);
        return view('detalleversion.create')->with(compact('versiones','categoria','propuesta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request
                          , VersionPropuesta $versiones)
    {
        //return $request->categoria;
        //
        $this->validate(request(),[
            'cantidad' => 'required',
            'preciou' => 'required',
            'porcentaje' => 'required',
            'cuenta' => 'required'
        ]);
        
        if($request->crearitem=="on"){
            $this->validate(request(),[
                'item_create' => 'required'
            ]);
            $item = ItemPropuesta::create([
                'nombre'=> $request->item_create
            ]);
            $item = $item->id;
        }else{
            $this->validate(request(),[
                'item' => 'required'
            ]);
            $item = $request->item;
        }

        if($request->crearunidad=="on"){
            $this->validate(request(),[
                'unidad_create' => 'required'
            ]);
            $unidad = UnidadItem::create([
                'nombre' => $request->unidad_create 
            ]);
            $unidad = $unidad->id;

        }else{
            $this->validate(request(),[
                'unidad' => 'required'
            ]);
            $unidad = $request->unidad;
        }
        
        DetalleVersionPropuesta::create([
            'id_version'=> $versiones->id,
            'id_categoria'=>$request->categoria,
            'losa'=>$request->losa,
            'id_item'=>$item,
            'cantidad'=>$request->cantidad,
            'id_unidad'=>$unidad,
            'precio_unitario'=>$request->preciou,
            'porcentaje_total'=>$request->porcentaje,
            'id_cuenta'=>$request->cuenta
        ]);
        return redirect(route('versiones.detalle.index'
                              ,['id'=>$versiones->id]));

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
        return view('detalleversion.show');
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
    public function destroy(VersionPropuesta $versiones, $id)
    {
        //
         DetalleVersionPropuesta::destroy($id);
         return redirect(route('versiones.detalle.index'
                              ,['id'=>$versiones->id]));
    }
}
