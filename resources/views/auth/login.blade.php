@extends('layouts.applogin')

@section('content')
    <div class="middle-box text-center animated swing">
        <div>
            <div>
                <img class="img-responsive" src="img/flexio_logo.jpg" ALT="Error cargando imagen" style="display: block;" id="imglogo">
            </div>
            <form class="m-t" role="form" method="POST" action="{{ url('/login') }}">
                {{ csrf_field() }}
                <div class="{{ $errors->has('email') ? ' has-error' : '' }}">
                    <div class="input-group m-b">
                        <span class="input-group-addon">@</span>
                        <input id="email" type="email" class="form-control input-lg" placeholder="Email" name="email" value="{{ old('email') }}">
                    </div>
                    @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                    @endif
                </div>
                
                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                    <input id="password" type="password" class="form-control input-lg" placeholder="Contraseña" name="password">
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b btn-lg">Login</button> 
                <small>
                    <a href="#">Olvide mi Contraseña</a> |
                    <a href="#">Crear Cuenta</a>
                    <p class="m-t">Desarrollado por Pensanomica &copy; 2016</p>
                </small>
            </form>
        </div>
    </div>
@endsection
