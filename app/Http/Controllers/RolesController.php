<?php

namespace App\Http\Controllers;

use App\CentroContable;
use App\CentroUser;
use App\Permiso;
use App\PermisoRol;
use App\Rol;
use App\RolUser;
use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
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
    public function index(){
        $users = User::orderBy('name')->get();
        foreach ($users as $user){
            $resp['rol']='Ninguno';
            $query = DB::table('role_user')->where("user_id",$user->id)->get();
            if ($query){
                $rolEntity = Rol::find($query[0]->role_id);
                $resp['rol']=$rolEntity->name; 
            }                    
            $resp['id']=$user->id;
            $resp['name']=$user->name;
            $resp['email']=$user->email;
           
            $items[] = $resp;
        }
        $roles = Rol::orderBy('Slug')->get();
        return view('set-rol')->with(['users'=>$items, 'roles'=>$roles] );
    }
    public function update(Request $request){
        $content = $request->all();
        $resp = 'not created';

        $user = isset($content['user'])?$content['user']: '';
        $rol = isset($content['rol'])?$content['rol']: '';

        $up = RolUser::updateOrCreate(
            ['user_id' => $user],
            ['role_id' => $rol]
        );
        if ($up != null){
            $resp = 'success';
        }
        return response()->json([
            'response' => $resp
        ]); 
    }
    public function index_edit(){
        $roles = Rol::orderBy('Slug')->get();
        return view('editar-permisos')->with(['roles'=>$roles] );
    }
    public function getPermisos(Request $request){
        $content = $request->all();
        $idRol = isset($content['rol'])?$content['rol']: '';
        $permisos = Permiso::all()->sortBy('description');
        
        
        
        foreach ($permisos as $permiso){
            
            $resp['assign']=0;
            $resp['name']=$permiso->name;
            $resp['id']=$permiso->id;
            $resp['description']=$permiso->description;
            $query = DB::table('permission_role')->where("role_id",$idRol)->where("permission_id",$permiso->id)->get();
            if (count($query))
                $resp['assign']=1;                    
            $items[] = $resp;
            
        }
        if($request->ajax()){
            return response()->json([
                'permisos' => $items
            ]);
        }
    }
    public function getCentrosUser(Request $request){
        $content = $request->all();
        $idUser = isset($content['user'])?$content['user']: '';
        $centros = CentroContable::all();
        foreach ($centros as $centro){
            $resp['assign']=0;
            $resp['name']=$centro->nombre_centro;
            $resp['id']=$centro->id_centro;
            $query = DB::table('centro_contable_user')->where("centro_contable_id",$centro->id_centro)->where("user_id",$idUser)->get();
            if (count($query))
                $resp['assign']=1;                    
            $items[] = $resp;   
        }
        if($request->ajax()){
            return response()->json([
                'centros_user' => $items
            ]);
        }
    }
    public function updatePermisos(Request $request){
        $content = $request->all();
        $resp = 'success';
        $idRol = isset($content['idRol'])?$content['idRol']: '';
        $permisosData = json_decode($content['array'], true);   
        if(count($permisosData)>0){
            foreach($permisosData as $item) {
                if($item['value']=='si')
                    PermisoRol::firstOrCreate(['permission_id' => $item['id_permiso'], 'role_id'=> $idRol]);
                else
                    PermisoRol::where('permission_id', $item['id_permiso'])->where('role_id', $idRol)->delete();
            }            
        }
        return response()->json([
            'response' => $resp
        ]);     
    }
    public function updateCentroUser(Request $request){
        $this->validate($request,[
            'user'=>'required',
            'centro'=>'required',
            'value'=>'required'
        ]);
        $resp = 'success';

        if($request->value == "true"){
            CentroUser::firstOrCreate(['centro_contable_id' => $request->centro, 'user_id'=> $request->user]);
        }
        else if($request->value == "false"){
            CentroUser::where('centro_contable_id', $request->centro)->where('user_id', $request->user)->delete();
        }
        $centro = CentroContable::find($request->centro);
        $user = User::find($request->user);
        return response()->json([
            'response' => $resp,
            'titulo' => ($request->value == "true" ? "Asignado": "Desasignado" ),
            'msg'=>'El centro '.$centro->nombre_centro.' ha sido '. ($request->value == "true" ? "asignado": "desasignado" ).' a '.$user->name.'.'
        ]);     
    }
}







       