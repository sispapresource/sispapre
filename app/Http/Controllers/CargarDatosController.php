<?php

namespace App\Http\Controllers;

use App\Adenda;
use App\AdendaDetalle;
use App\CentroContable;
use App\Cuenta;
use App\Factura;
use App\OrdenCompra;
use App\Prueba;
use App\Http\Requests;
use App\PresupuestoAvance;
use App\ItemPropuesta;
use App\PresupuestoItem;
use App\UnidadItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Alert, Session;

class CargarDatosController extends Controller{
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
    public function index(){


        $proyectos = CentroContable::orderBy('centros_contables.id_centro', 'asc')->get();
        return view('cargar-datos')->with(['proyectos' => $proyectos]);
    }

    public function postUploadCsv(Request $request){
        if(($request->hasFile('document'))&&($request->get('selectcentro'))&&($request->get('selectcarga'))){
            $path = $request->file('document')->getRealPath();
            $data = Excel::load($path, function($reader) {})->get();

            if(($request->get('selectcarga'))=='presupuesto'){
                $total_p=0;
                if(!empty($data) && $data->count()){

                    foreach ($data->toArray() as $key => $value){
                        try{


                            if((strlen($value['codigo'])>0)&&(strlen($value['presupuesto'])>0)){
                                $parts = explode('.',$value['codigo']);
                                $cuenta = Cuenta::firstOrCreate([
                                    'id_cuenta' => $value['codigo'],
                                    'categoria'=>'-',
                                    'nombre_cuenta'=>'-',
                                    'nivel'=>0,
                                    'id_padre'=>$parts[0].'.'.$parts[1].'.'
                                ]);

                                if($cuenta->categoria!='Totalizador'){

                                    $total_p++;
                                    PresupuestoAvance::updateOrCreate(
                                        ['id_cuenta' => $value['codigo'], 'id_centro_contable' => $request->get('selectcentro')],
                                        ['presupuesto' => $value['presupuesto']]
                                    );
                                   

                                }
                            }
                        }catch(\Exception $e){
                            Alert::error('El formato del documento es incorrecto, por favor consulte el manual de usuario.')->persistent("Close");
                            return back();
                        }
                    }
                    Alert::success('Total presupuestos insertados/actualizados (' .$total_p. ').')->persistent("Close");
                    return back();
                }

            }
            if(($request->get('selectcarga'))=='avance'){
                $total_a=0;
                if(!empty($data) && $data->count()){
                    $errores = [];
                    foreach ($data->toArray() as $key => $value){
                        try{
                            if((strlen($value['codigo'])>3)&&(strlen($value['avance'])>0)){

                                if($value['avance']>1){
                                    $value['avance']=1;
                                    array_push($errores,'Porcentaje mayor a 100 se se asumió 100. En: '.$value['codigo']);
                                }



                                try {

                                    $parts = explode('.',$value['codigo']);
                                    $cuenta = Cuenta::firstOrCreate([
                                        'id_cuenta' => $value['codigo'],
                                        'categoria'=>'-',
                                        'nombre_cuenta'=>'-',
                                        'nivel'=>0,
                                        'id_padre'=>$parts[0].'.'.$parts[1].'.'
                                    ]);
                                    
                                    if($cuenta->categoria!='Totalizador'){
                                        $pre = PresupuestoAvance::where('id_cuenta','=',$cuenta->id_cuenta)
                                            ->where('id_centro_contable','=',$request->get('selectcentro'))
                                            ->get();
                                        try{

                                            $pre = $pre->first();
                                            $pre->porcentaje_avance = $value['avance']*100;
                                            $pre->save();
                                            $total_a++;
                                        }catch(\Exception $e){
                                            array_push($errores,'La cuenta no está asociada al proyecto. En: '.$value['codigo']);        
                                        }

                                    }   
                                }catch (\Exception $e) { // I don't remember what exception it is specifically
                                    array_push($errores,'El número de cuenta no existe en el sistema. En: '.$value['codigo']);
                                    /*Alert::error('Ocurrió un error al cargar el archivo por favor revise los datos cerca de la cuenta '.$value['codigo'],'Oops!')->persistent('Close');
                                    return redirect()->back();*/
                                }

                            }
                        }catch(\Exception $e){
                            Session::flash('errores', $errores);
                            Alert::error('El formato del documento es incorrecto, por favor consulte el manual de usuario.')->persistent("Close");
                            return back();
                        }
                    }
                    Session::flash('errores', $errores);
                    Alert::success('Total porcentajes insertados/actualizados (' .$total_a. ').')->persistent("Close");
                    return back();
                }
            }
            
            if(($request->get('selectcarga'))=='cantidades'){
                $total_a=0;
                $errores = [];
                if(!empty($data) && $data->count()){
                    foreach ($data->toArray() as $key => $value){
                        try{
                            $cuenta = Cuenta::find($value["cuenta"]);
                            if($cuenta != null){
                                $item = ItemPropuesta::firstOrCreate([
                                    "nombre"=> (string)$value["item"]
                                ]);
                                $unidad = UnidadItem::firstOrCreate([
                                    "nombre"=> (string)$value["unidad"]
                                ]);
                                PresupuestoItem::updateOrCreate([
                                    'id_centro'=> $request->get('selectcentro'),
                                    'id_cuenta'=>$cuenta->id_cuenta,
                                    'id_propuesta_items'=>$item->id,
                                    'id_unidad'=>$unidad->id,
                                ],[
                                    'cantidad'=>$value["cantidad"],
                                    'PrecioUnitario'=>$value["pu"]
                                ]);    
                                $total_a++;
                            }
                        }catch(\Exception $e){
                            Alert::error('El formato del documento es incorrecto, por favor consulte el manual de usuario.')->persistent("Close");
                            return back();
                        }

                    }
                    Session::flash('errores', $errores);
                    Alert::success('Total presupuestos insertados/actualizados (' .$total_a. ').')->persistent("Close");
                    return back();
                }
            }   

            if(($request->get('selectcarga'))=='teorico'){
                $total_a=0;
                $errores = [];
                if(!empty($data) && $data->count()){
                    foreach ($data->toArray() as $key => $value){
                        try{
                            if((strlen($value['codigo'])>3)&&(strlen($value['avance'])>0)){

                                if($value['avance']>1){
                                    $value['avance']=1;
                                    array_push($errores,'Porcentaje mayor a 100% se se asumió 100%. En: '.$value['codigo']);
                                }

                                
                                try {
                                    $parts = explode('.',$value['codigo']);
                                    $cuenta = Cuenta::firstOrCreate([
                                        'id_cuenta' => $value['codigo'],
                                        'categoria'=>'-',
                                        'nombre_cuenta'=>'-',
                                        'nivel'=>0,
                                        'id_padre'=>$parts[0].'.'.$parts[1].'.'
                                    ]);

                                    if($cuenta->categoria!='Totalizador'){
                                        $pre = PresupuestoAvance::where('id_cuenta','=',$value['codigo'])
                                            ->where('id_centro_contable','=',$request->get('selectcentro'))
                                            ->get();
                                        try{

                                            $pre = $pre->first();
                                            $pre->porcentaje_teorico = $value['avance']*100;
                                            $pre->save();
                                            $total_a++;
                                        }catch(\Exception $e){
                                            array_push($errores,'La cuenta no está asociada al proyecto. En: '.$value['codigo']);        
                                        }

                                    }   
                                }catch (\Exception $e) { // I don't remember what exception it is specifically
                                    array_push($errores,'El número de cuenta no existe en el sistema. En: '.$value['codigo']);
                                    /*Alert::error('Ocurrió un error al cargar el archivo por favor revise los datos cerca de la cuenta '.$value['codigo'],'Oops!')->persistent('Close');
                                    return redirect()->back();*/
                                }

                            }
                        }catch(\Exception $e){
                            Alert::error('El formato del documento es incorrecto, por favor consulte el manual de usuario.')->persistent("Close");
                            return back();
                        }

                    }
                    Session::flash('errores', $errores);
                    Alert::success('Total porcentajes insertados/actualizados (' .$total_a. ').')->persistent("Close");
                    return back();
                }
            }   


        }
        Alert::error('El formato del archivo cargado no es válido. Consulte el manual de uso para utilizar el formato correcto')->persistent("Close");
        return back();
    }
    /*public function postCuentas(Request $request){
	    if($request->hasFile('document')){
	        $path = $request->file('document')->getRealPath();
	        $data = Excel::load($path, function($reader) {})->get();
	        if(!empty($data) && $data->count()){
	            foreach ($data->toArray() as $key => $value){
					$insert[]=['id_cuenta' => $value['id_cuenta'], 'nombre_cuenta' => $value['nombre_cuenta'], 'nivel' => $value['nivel'], 'id_padre' => $value['id_padre']];					
	            }
                if(!empty($insert))
	                Cuenta::insert($insert);
	        }
	    }
    	return back();
	}*/
}

