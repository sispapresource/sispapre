<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\VersionPropuesta;
use App\EstadoPropuesta;
use App\ItemPropuesta;
use App\CategoriaPropuesta;
use App\DetalleVersionPropuesta;
use App\UnidadItem;
use App\Propuesta;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;


class VersionPropuestaController extends Controller
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


    public function index(Request $request)
    {
        //
        return "bla from versionpropuestacontroller";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //
        $propuesta = Propuesta::find($id);
        return view('versionpropuesta.create')->with(compact('propuesta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //07/12/2017



        $propuesta = Propuesta::find($request->idPropuesta);
        $this->validate(request(),[
            'document' => 'required',
            'fecha' => 'required'
        ]);
        $fecha =  Carbon::createFromFormat('m/d/Y', $request->fecha);
        if($fecha ===""){
            $fecha=Carbon::now();
        }
        //        $nombrearchivo = request()->file('document')->getClientOriginalExtension();
        $nombrearchivo = $request->file('document')->getClientOriginalName();
        $url ='/public/documents/' .$propuesta->cuenta->nombre_centro ."/";



        $version = VersionPropuesta::create([
            'fecha_creacion' =>  Carbon::now(),
            'valido_hasta' => $fecha,
            'monto_total' => 100,
            'id_estado'=>1,
            'carta_propuesta'=>$nombrearchivo,
            'id_usuario'=>$propuesta->usuario->id,
            'id_propuesta'=>$propuesta->id,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now()
        ]);

        $path = $request->file('document')->getRealPath();
        Excel::selectSheetsByIndex(0)->load($path, function ($sheet) use($version){
            $sheet->noHeading();
            $sheet->each(function($row) use ($version){
                if((string)$row[1]!="Item"){
                    $item = ItemPropuesta::firstOrCreate([
                        "nombre"=> (string)$row[1]
                    ]);
                    $categoria = CategoriaPropuesta::firstOrCreate([
                        "nombre"=> (string)$row[3]
                    ]);
                    $unidad = UnidadItem::firstOrCreate([
                        "nombre"=> (string)$row[6]
                    ]);

                    $detalle = DetalleVersionPropuesta::create([
                        'id_version'=>$version->id, 
                        'id_categoria'=>$categoria->id, 
                        'losa'=>$row[4], 
                        'id_item'=>$item->id, 
                        'cantidad'=>(int)$row[5], 
                        'id_unidad'=>$unidad->id, 
                        'precio_unitario'=>(float)$row[7], 
                        'total'=>(float)$row[8], 
                        'porcentaje_total'=>(float)$row[9], 
                        'id_cuenta'=>(string)$row[10] 
                    ]);

                }
            });
        });

        $nombrecompleto=$version->id . "_" . $propuesta->id . "_" . $propuesta->cuenta->id_centro . "_Propuesta_" .$nombrearchivo;
        request()->file('document')->move(
            base_path() . $url, $nombrecompleto
        );
        return redirect()->route('propuestas.show', ['id' => $propuesta->id]);

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
        $version = VersionPropuesta::find($id);
        return view('versionpropuesta.show')->with(compact('version'));
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
        $version = VersionPropuesta::find($id);
        $propuesta = Propuesta::find($version->id_propuesta);
        return view('versionpropuesta.edit')->with(compact('propuesta','version'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $versiones)
    {
        //  
        $version = VersionPropuesta::find($versiones);
        $propuesta = Propuesta::find($version->id_propuesta);
        $this->validate(request(),[
            'document' => 'required',

        ]);
        if($request->has('fecha')){
            $fecha =  Carbon::createFromFormat('m/d/Y', $request->fecha);
        }
        $fecha = $version->valido_hasta;
        //        $nombrearchivo = request()->file('document')->getClientOriginalExtension();
        $nombrearchivo = request()->file('document')->getClientOriginalName();
        $url ='/public/documents/' .$propuesta->cuenta->nombre_centro ."/";


        $version->fecha_creacion = Carbon::now();
        $version->valido_hasta =$fecha;
        $version->carta_propuesta=$nombrearchivo;
        $version->updated_at= Carbon::now();
        $version->save();

        DetalleVersionPropuesta::where('id_version',$version->id)->delete();
        $path = $request->file('document')->getRealPath();
        Excel::selectSheetsByIndex(0)->load($path, function ($sheet) use($version){
            $sheet->noHeading();
            $sheet->each(function($row) use ($version){
                if((string)$row[1]!="Item"){
                    $item = ItemPropuesta::firstOrCreate([
                        "nombre"=> (string)$row[1]
                    ]);
                    $categoria = CategoriaPropuesta::firstOrCreate([
                        "nombre"=> (string)$row[3]
                    ]);
                    $unidad = UnidadItem::firstOrCreate([
                        "nombre"=> (string)$row[6]
                    ]);

                    $detalle = DetalleVersionPropuesta::create([
                        'id_version'=>$version->id, 
                        'id_categoria'=>$categoria->id, 
                        'losa'=>$row[4], 
                        'id_item'=>$item->id, 
                        'cantidad'=>(int)$row[5], 
                        'id_unidad'=>$unidad->id, 
                        'precio_unitario'=>(float)$row[7], 
                        'total'=>(float)$row[8], 
                        'porcentaje_total'=>(float)$row[9], 
                        'id_cuenta'=>(string)$row[10] 
                    ]);

                }
            });
        });


        $nombrecompleto=$version->id . "_" . $propuesta->id . "_" . $propuesta->cuenta->id_centro . "_Propuesta_" .$nombrearchivo;
        request()->file('document')->move(
            base_path() . $url, $nombrecompleto
        );
        return redirect()->route('propuestas.show', ['id' => $propuesta->id]);


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function estado(Request $request, $id)
    {
        //
        $version = VersionPropuesta::find($id);
        $estados = EstadoPropuesta::all()->pluck('nombre','id');
        $propuesta = Propuesta::find($version->id_propuesta);
        return view('versionpropuesta.cambiarestado')->with(compact('version','estados','propuesta'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function saveestado(Request $request, $id)
    {
        //
        $version = VersionPropuesta::find($id);
        $version->id_estado = $request->estado;
        $version->save();
        return redirect()->route('propuestas.show', ['id' => $version->id_propuesta]);

        //$version = VersionPropuesta::find($id);
        //$estados = EstadoPropuesta::all()->pluck('nombre','id');

    }

    /**
     * Download file 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function cartapropuesta($id){
        $version=VersionPropuesta::find($id);
        $nombrearchivo = $version->carta_propuesta;
        $nombrecompleto=$version->id . "_" . $version->propuesta->id . "_" . $version->propuesta->cuenta->id_centro . "_Propuesta_" .$nombrearchivo;

        $url =base_path() . '/public/documents/' .$version->propuesta->cuenta->nombre_centro."/".$nombrecompleto;

        return response()->download($url);
    }


}
