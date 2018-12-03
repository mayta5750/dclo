<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"><!---->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OEI</title>

   <!-- <title>{{ config('app.name', 'OEI') }}</title>-->

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <!--
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    -->

    <!-- Styles -->
    <!--<link href="{{ asset('css/app.css') }}" rel="stylesheet">-->

    <link rel="stylesheet" href="{!! asset('assets/css/bootstrap.min.css') !!}">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <!--<link href="{{ asset('css/style.css') }}" rel="stylesheet">-->
 
</head>

<body>
    <div style="background:#dff0d8">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <div>
            <a class="navbar-brand" href="{{ url('/words') }}">
                    <img src="{!! asset('storage/dclo_public/micelanea/casa.png') !!}" width="30" height="30" class="d-inline-block align-top" alt="">
            </a>
            </div>
            <div class="container" >
                <!--
                    <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                -->
                <div>
                    <h1 class="h2" style="color:white">OEI</h1>
                </div>
                
                <?php 
                //dd(isset(Auth::user()->tipoUsuario));
                if(isset(Auth::user()->tipoUsuario)){
                if(Auth::user()->tipoUsuario==1){?>
                <div class="dropdown col-md-2">
                    <button class="btn btn-secondary dropdown-toggle btn-dark" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Usuarios
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <button onclick="location.href='{{ url('/register') }}'" class="dropdown-item btn-dark" type="button">Adicionar</button>
                        <button onclick="location.href='{{ url('/users') }}'" class="dropdown-item btn-dark" type="button">Usuarios</button>
                    </div>
                </div>
                <div class="dropdown col-md-2">
                    <button class="btn btn-secondary dropdown-toggle btn-dark" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Idioma
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <button onclick="location.href='{{ url('/create') }}'" class="dropdown-item btn-dark" type="button">Adicionar</button>
                        <button onclick="location.href='{{ url('/languages') }}'" class="dropdown-item btn-dark" type="button">Idiomas</button>
                    </div>
                </div>
                <?php ; } 
                }?>
                
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            <li class="nav-item">
                                @if (Route::has('register'))
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                @endif
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ url('password') }}">
                                        {{ __('Cambiar Contraseña') }}
                                    </a>
                                    <form id="password-form" action="{{ url('password') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Salir') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </div>
        <main class="py-4">
            @yield('content')
        </main>