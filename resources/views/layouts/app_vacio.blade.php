<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"><!---->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Styles -->
    <link rel="stylesheet" href="{!! asset('assets/css/bootstrap.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('assets/css/styles.css') !!}">
</head>
<body>
    <div style="background:#dff0d8">
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <div class="container" >
            <div>
                    <h1 class="h2" style="color:white">OEI</h1>
            </div>
               
            </div>
        </nav>
    </div>
        <main class="py-4">
            @yield('content')
        </main>
    

