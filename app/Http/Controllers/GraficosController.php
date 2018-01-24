<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class GraficosController extends Controller
{
    public function getData(Request $request){
        return view('grafico');
    }
}
