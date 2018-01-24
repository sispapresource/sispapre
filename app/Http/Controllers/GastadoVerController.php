<?php

namespace App\Http\Controllers;

use App\CentroContable;
use App\Cuenta;
use App\Factura;
use App\OrdenCompra;
use App\Planilla;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\Input;


class GastadoVerController extends Controller
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
    public function getGastado(Request $request){
        
        $items = null; 
        $centroContable = CentroContable::find($request->idCentro);
        $gastado = (new Factura)->newQuery();
        $planillas = (new Planilla)->newQuery(); 
        
        if($request->has('categorias')){
            
            $gastado->whereHas('cuenta',function($c) use($request){
                $c->whereIn('categoria',$request->categorias);
            });
            
            $planillas->whereHas('cuenta',function($c) use($request){
                $c->whereIn('categoria',$request->categorias);
            });
        }
        
        if($request->has('idCentro')){
            $gastado->where('id_centro',(int)$request->idCentro);
            $planillas->where('id_centro',(int)$request->idCentro);
        } 
        if($request->has('cuentas')){
            foreach($request->cuentas as $cuenta){
                $gastado->where('id_cuenta','like',$cuenta.'%');
                $planillas->where('id_cuenta','like',$cuenta.'%');
            }
        }   
        
        if($request->has('idCuenta')){
            $gastado->where('id_cuenta','like',$request->idCuenta.'%');
            $planillas->where('id_cuenta','like',$request->idCuenta.'%');
            
            if(!$request->has('cuentas')){
                $request->cuentas = [];
            }
            array_push($request->cuentas,$request->idCuenta);
            
        }
        
        if($request->has('dateDesde')){
            $date_init = Carbon::createFromFormat('m/d/Y',$request->dateDesde)->format('Y/m/d');
            $gastado->where('fecha_transaccion','>=',$date_init);
            $planillas->where('fecha_transaccion','>=',$date_init);
        }
        if($request->has('dateHasta')){
            $date_init = Carbon::createFromFormat('m/d/Y',$request->dateHasta)->format('Y/m/d');
            $gastado->where('fecha_transaccion','<=',$date_init);
            $planillas->where('fecha_transaccion','<=',$date_init);
        } 
        if($request->has('montoDesde')){
            $gastado->where('monto','>=',(float)$request->montoDesde);
            $planillas->where('monto','>=',(float)$request->montoDesde);
        }
        if($request->has('montoHasta')){
            $gastado->where('monto','<=',(float)$request->montoHasta);
            $planillas->where('monto','<=',(float)$request->montoHasta);
        } 
        
        if($request->has('proveedor')){
            $gastado->where('nombre_proveedor','like',$request->proveedor.'%');
            $planillas->take(0);
        } 
        
        $items = $planillas->get()->toBase()->merge($gastado->get()->toBase());
        
        $arr = $items->toArray();
        $page = Input::get('page', 1); // Get the ?page=1 from the url
        $perPage = 25; // Number of items per page
        $offset = ($page * $perPage) - $perPage;
        $arr_splice = array_slice($arr, $offset, $perPage, true);
        $paginator = new Paginator($arr_splice, count($arr), $perPage, $page);
        $paginator->setPath($request->url());
        $paginator->appends($request->all());
        
        $proyectos = DB::table('centros_contables')
        ->select('id_centro','nombre_centro')
        ->join('centro_contable_user', 'centros_contables.id_centro', '=', 'centro_contable_user.centro_contable_id')
        ->where('centros_contables.totalizador',0)
        ->where('centro_contable_user.user_id',auth()->id())
        ->orderBy('centros_contables.id_padre', 'asc')
        ->orderBy('centros_contables.id_centro', 'asc')->pluck('nombre_centro','id_centro');
        $cuentas = Cuenta::select('id_cuenta','nombre_cuenta')->get();
        
        return view('gastado-ver')->with([
            'nombre_proveedor'=> $request->proveedor, 
            'categorias'=>$request->categorias,
            'fecha_desde'=>$request->dateDesde,
            'fecha_hasta'=>$request->dateHasta,
            'monto_desde'=>$request->montoDesde,
            'monto_hasta'=>$request->montoHasta,
            'id_cuenta'=>$request->cuentas,
            'gastado'=>$gastado, 
            'planillas'=>$planillas, 
            'items' => $items,
            'paginator' => $paginator,
            'proyectos' => $proyectos,
            'id_centro' => $request->idCentro, 
            'cuentas' => $cuentas
            ] );
        }
        
        public function makeItemsGastado($gastado){
            foreach ($gastado as $gasto){
                $add = true;
                if(isset($idCuenta) && $idCuenta != ''){
                    if(!is_array($idCuenta))  
                    $idCuenta = explode(" ", $idCuenta);
                    $add = false;
                    foreach($idCuenta as $value){
                        if((stripos($gasto->id_cuenta,$value))!==false)
                        $add = true;
                    }
                }
                if(isset($proveedorFilter) && $proveedorFilter != ''){
                    if(stripos($gasto->nombre_proveedor,$proveedorFilter)===false)
                    $add = false;
                }
                if(isset($idCentro) && $idCentro != ''){
                    if($idCentro!=$gasto->id_centro)
                    $add = false;
                }
                if(isset($dateFilterDesde)&& $dateFilterDesde!= ''){
                    $date_init = strtotime($dateFilterDesde);
                    $ua_comp = strtotime(isset($gasto->fecha_transaccion)? $gasto->fecha_transaccion:'1999-01-01 00:00:00');
                    if($ua_comp < $date_init)
                    $add = false;
                }
                if(isset($dateFilterHasta)&& $dateFilterHasta!= ''){
                    $date_init = strtotime($dateFilterHasta);
                    $ua_comp = strtotime(isset($gasto->fecha_transaccion)? $gasto->fecha_transaccion:'1999-01-01 00:00:00');
                    if($ua_comp > $date_init)
                    $add = false;
                }
                if(isset($montoFilterDesde)&& $montoFilterDesde!= ''){
                    if($montoFilterDesde > $gasto->monto)
                    $add = false;
                }
                if(isset($montoFilterHasta)&& $montoFilterHasta!= ''){
                    if($montoFilterHasta < $gasto->monto)
                    $add = false;
                }
                if($add){
                    $resp['id_cuenta']= $gasto->id_cuenta;
                    $resp['fecha']= $gasto->fecha_transaccion;
                    $resp['proveedor']= $gasto->nombre_proveedor;
                    $resp['nro']= $gasto->num_fact;
                    $resp['descripcion']=$gasto->desc_transaccion;
                    $resp['monto']=$gasto->monto;
                    $items[] = $resp;
                }
            }
            return $items;
        }
        public function makeItemsPlanilla($planilla){
            foreach ($planilla as $plan){
                $add = true;
                if(isset($idCuenta) && $idCuenta != ''){
                    if(!is_array($idCuenta))  
                    $idCuenta = explode(" ", $idCuenta);
                    $add = false;
                    foreach($idCuenta as $value){
                        if((stripos($plan->id_cuenta,$value))!==false)
                        $add = true;
                    }
                }
                if(isset($proveedorFilter) && $proveedorFilter != ''){
                    if(stripos($plan->nombre_proveedor,$proveedorFilter)===false)
                    $add = false;
                }
                if(isset($idCentro) && $idCentro != ''){
                    if($idCentro!=$plan->id_centro)
                    $add = false;
                }
                if(isset($dateFilterDesde)&& $dateFilterDesde!= ''){
                    $date_init = strtotime($dateFilterDesde);
                    $ua_comp = strtotime(isset($plan->fecha_transaccion)? $plan->fecha_transaccion:'1999-01-01 00:00:00');
                    if($ua_comp < $date_init)
                    $add = false;
                }
                if(isset($dateFilterHasta)&& $dateFilterHasta!= ''){
                    $date_init = strtotime($dateFilterHasta);
                    $ua_comp = strtotime(isset($plan->fecha_transaccion)? $plan->fecha_transaccion:'1999-01-01 00:00:00');
                    if($ua_comp > $date_init)
                    $add = false;
                }
                if(isset($montoFilterDesde)&& $montoFilterDesde!= ''){
                    if($montoFilterDesde > $plan->monto)
                    $add = false;
                }
                if(isset($montoFilterHasta)&& $montoFilterHasta!= ''){
                    if($montoFilterHasta < $plan->monto)
                    $add = false;
                }
                if($add){
                    $resp['id_cuenta']= $plan->id_cuenta;
                    $resp['fecha']= $plan->fecha_transaccion;
                    $resp['proveedor']= '';
                    $resp['nro']= $plan->num_planilla;
                    $resp['descripcion']=$plan->desc_transaccion;
                    $resp['monto']=$plan->monto;
                    $items[] = $resp;
                }
            }
            return $items;
        }
        
        public function getComprometido(Request $request){
            
            $comprometidos = (new OrdenCompra)->newQuery();
            $cuentas = Cuenta::all();
            $proyectos = CentroContable::all();

            
            if($request->has('categorias')){
                $comprometidos->whereHas('cuenta',function($c) use($request){
                    $c->whereIn('categoria',$request->categorias);
                });
            }
            
            if($request->has('idCentro')){
                $comprometidos->where('id_centro',$request->idCentro);
            }

            if($request->has('idCuenta')){
                $comprometidos->where('id_cuenta','like',$request->idCuenta.'%');
                
                if(!$request->has('selCuentas')){
                    $request->selCuentas = [];
                }
                array_push($request->selCuentas,$request->idCuenta);
                
            }            
            if($request->has('selCuentas')){
                foreach($request->selCuentas as $cuenta){
                    $comprometidos->where('id_cuenta','like',$cuenta.'%');
                }
            }  

            if($request->has('fechaDesde')){
                $date_init = Carbon::createFromFormat('m/d/Y',$request->fechaDesde)->format('Y/m/d');
                $comprometidos->where('fecha_transaccion','>=',$date_init);
            }
            if($request->has('fechaHasta')){
                $date_init = Carbon::createFromFormat('m/d/Y',$request->fechaHasta)->format('Y/m/d');
                $comprometidos->where('fecha_transaccion','<=',$date_init);
            } 
            if($request->has('montoDesde')){
                $comprometidos->where('monto_compra','>=',(float)$request->montoDesde);
            }
            if($request->has('montoHasta')){
                $comprometidos->where('monto_compra','<=',(float)$request->montoHasta);
            } 

            return view('comprometido')->with([
                // 'nombre_proveedor'=> $proveedorFilter, 
                'categorias'=>$request->categorias,
                'fecha_desde'=>$request->fechaDesde,
                'fecha_hasta'=>$request->fechaHasta,
                'monto_desde'=>$request->montoDesde,
                'monto_hasta'=>$request->montoHasta,
                'selCuentas'=>$request->selCuentas,
                'comprometidos'=>$comprometidos->get(), 
                'proyectos' => $proyectos, 
                'idCentro' => $request->idCentro, 
                'cuentas' => $cuentas
                ]);
          
            
        }
        public function ordenado($array,$item){
            $j=0;
            $flag = true;
            $temp=0;
            while ( $flag )
            {
              $flag = false;
              for( $j=0;  $j < count($array)-1; $j++)
              {
                $array_cuenta_first=explode(".",$array[$j]['id_cuenta']);
                $array_cuenta_next=explode(".",$array[$j+1]['id_cuenta']);  
                if ((int)$array_cuenta_first[$item]>(int)$array_cuenta_next[$item] )
                {
                      $temp = $array[$j];
                      $array[$j] = $array[$j+1];
                      $array[$j+1]=$temp;                  
                      $a=1;
                      $flag = true;
                }                 
                }
            }
            return $array;
    }     
    }
    