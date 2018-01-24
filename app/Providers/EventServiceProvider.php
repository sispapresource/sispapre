<?php

namespace App\Providers;


use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\LogCambios;
use App\PresupuestoAvance;
use Illuminate\Support\Facades\Auth;
class EventServiceProvider extends ServiceProvider
{
    /**
    * The event listener mappings for the application.
    *
    * @var array
    */
    
    protected $listen = [
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\SuccessfulLogin',
        ],
    ];
    
    protected $models_to_log=[
        '0' => [
            'name'=>'\\App\\PresupuestoAvance',
            'id_centro'=>'id_centro_contable',
            'id_cuenta'=>'id_cuenta'
        ],
        '1'=>[
            'name'=>'\\App\\Factura',
            'id_centro'=>'id_centro',
            'id_cuenta'=>'id_cuenta'
        ],
        '3'=>[
            'name'=>'\\App\\Planilla',
            'id_centro'=>'id_centro',
            'id_cuenta'=>'id_cuenta'
        ],
        '4'=>[
            'name'=>'\\App\\OrdenCompra',
            'id_centro'=>'id_centro',
            'id_cuenta'=>'id_cuenta'
        ],
        // '5'=>[
        //     'name'=>'\\App\\Adenda',
        //     'id_centro'=>'id_centro',
        //     'id_cuenta'=>'id_cuenta'
        // ],
    ];
    
    /**
    * Register any other events for your application.
    *
    * @param  \Illuminate\Contracts\Events\Dispatcher  $events
    * @return void
    */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);
        
        foreach($this->models_to_log as $table){
            $instance = new $table['name'];
            
            $instance::saving(
                function($model) use($table){
                    try {
                        //Se encuentra la diferencia de los objetos
                        $diferencias= array_diff_assoc($model->getAttributes(),$model->getOriginal());
                        
                        foreach($diferencias as $key => $diferencia){
                            $log = new \App\LogCambios();
                            $log->id_centro= $model->getAttributes()[$table['id_centro']];
                            $log->id_cuenta= $model->getAttributes()[$table['id_cuenta']];
                            
                            $log->id_usuario =  Auth::id();
                            $log->fecha_entrada= \Carbon\Carbon::now();
                            $log->descripcion_cambio=$table['name'].'->'.$key;
                            $log->valor_anterior=isset($model->getOriginal()[$key])? $model->getOriginal()[$key]: 0;
                            $log->valor_nuevo=$diferencia;
                            $log->save();
                        }
                    }catch(Exeption $e){
                        report($e);
                    }
                    
                    return true;
                }
            );
        }
        
    }
}
