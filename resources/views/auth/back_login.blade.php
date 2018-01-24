@extends('layouts.applogin')

@section('content')
    <div class="middle-box text-center animated swing">
        <div>
            <div>
                <img class="img-responsive" src="img/flexio_logo.jpg" ALT="Error cargando imagen" style="display: block;" id="imglogo">
            </div>
            <form class="m-t" role="form" method="POST" action="{{ url('/set_login') }}">
                {{ csrf_field() }}
                    <input id="email" name="email" type="hidden" value="{{ $email }}">
                    <input id="password" name="password" type="hidden" value="{{ $password }}">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b btn-lg">Login</button> 
            </form>
        </div>
    </div>
@endsection
