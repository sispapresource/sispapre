<?php

namespace App\Listeners;

use App\LogLogin;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $loginAttempt = LogLogin::create([
            'id_usuario' => auth()->id(),
            'fecha' => Carbon::now(),
        ]);
    }
}
