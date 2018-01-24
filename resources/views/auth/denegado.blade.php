@extends('layouts.applogin')

@section('content')
    <div class="middle-box text-center animated swing">
        <div>
            <div>
                <img class="img-responsive" src="img/flexio_logo.jpg" ALT="Error cargando imagen" style="display: block;" id="imglogo">
            </div>
            ACCESO DENEGADO!!!
            @include('layouts.errors')
        </div>
    </div>
@endsection
