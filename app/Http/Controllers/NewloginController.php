<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use App\User;
use Auth, Validator, Session;

class NewloginController extends Controller
{

    public function login(Request $request){
        if(Auth::check()){
            Session::flush();
        }
        //$token = "(7(=!/JSG&J81Dz|cd?GD183vkLL$;H{yD!&KZj#7so&ViyET=";
        $token = "flexio";
        
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);
        if ($validator->fails()) {
            return view('auth.denegado')
            ->withErrors($validator);
        }
        
        if(strcmp($token,$request->token)!==0){
            $validator->errors()->add('token', 'Los tokens no coinciden');
            return view('auth.denegado')
            ->withErrors($validator);
        }
        $user = User::where('email',$request->email)->get()->first();
        if($user && Auth::loginUsingId($user->id)){
            return redirect('/');
        }
        return view('auth.denegado');
    }
}
