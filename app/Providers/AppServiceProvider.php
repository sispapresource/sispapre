<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\CentroContable;
use App\LineaDeVenta;
use App\ItemPropuesta;
use App\UnidadItem;
use App\Cuenta;
use App\Cliente;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $centros = CentroContable::where('totalizador',0)->pluck('nombre_centro','id_centro');
        $lineaventa = LineaDeVenta::pluck('nombre','id');
        $itemprop = ItemPropuesta::pluck('nombre','id');
        $unidad = UnidadItem::pluck('nombre','id');
        $cuentas = Cuenta::pluck('id_cuenta','id_cuenta');
        $clientes = Cliente::pluck('nombre','id');

        View::share('centros',$centros);
        View::share('lineaventa',$lineaventa);
        View::share('itemprop',$itemprop);
        View::share('unidad',$unidad);
        View::share('cuentas',$cuentas);
        View::share('clientes',$clientes);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
