<?php

namespace App\Http\Controllers;

use App\LogLogin;
use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogLoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $items = null;

        $logs = LogLogin::orderBy('fecha', 'desc')->get();

        foreach ($logs as $item){

            $UserEntity = User::find($item->id_usuario);

            $resp['fecha']=$item->fecha;
            $resp['usuario']= $UserEntity->name;
            $items[] = $resp;
        }
        
        return view('log-login')->with(['login_usuarios'=>$items] );
    }
}