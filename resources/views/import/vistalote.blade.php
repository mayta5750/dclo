
@extends('layouts.app')

@section('content')
<div id="borde" class="borde" style="padding:80px 100px 0 100px">
<div class="row">
    <div class = "col-md-6">
        <h1><strong>Lote de Idiomas</strong></h1>
        <h3>Idioma: {!! $idioma !!}</h3>
        <h3>Cantidad de palabras: {{App\Http\Controllers\TestController::contarPalabra($fecha,$id_language_from)}}</h3>
        <h4><strong>{{$fecha->formatLocalized('%d - %B - %Y %H:%I:%S')}}</strong></h4>
        <i>({{$fecha->diffForHumans()}})</i>
    </div>
    <div class = "col-md-3">
        <button type="button" class="btn btn-outline-success" onclick="location.href='{{ url('words') }}'">
            Guardar
        </button>
    </div>
    <div class = "col-md-3">
        <button type="button" class="btn btn-outline-danger" onclick="location.href='eliminar/{!! $fecha,'_'.$nueva_carpeta !!}'">
            Deshacer
        </button>
    </div>
</div>
<hr/>
@if(Session::has('message'))
<div class="alert-custom">
    <p>{!! Session('message') !!}</p>
</div>
@endif()
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th style="padding-left: 15px;">#</th>
            <th>PALABRA</th><!--palabra (grapheme)-->
            <th>CASTELLANO</th><!--palabra (grapheme)-->
            <th>IMAGENES</th><!--meaning-->
            <th>DEFINICIONES</th>
            <th>AUDIOS</th>
            <th>IDIOMA</th>
            <th width="110px;">EDITAR</th>
        </tr>
        </thead>
        <tbody>
        @foreach($word as $item)
        <tr>
            <th style="vertical-align:middle;">{{$item->id_word}}</th>
            <td style="vertical-align:middle;">{{$item->grapheme}}</td>
            <td style="vertical-align:middle;">{{App\Http\Controllers\TestController::castellano($item->id_word_definition)}}</td>
            <td style="vertical-align:middle;"><img style="width:90px;height: 90px;"src="{{$item->path==null? asset('storage/dclo_public/micelanea/nada.svg') : 'http://web.oei.bo/dclo_public/img_out_L/L_'.$item->path }}" class="img-responsive img-rounded img" /></td>
            <td style="vertical-align:middle;">{{$item->meaning}}</td>
            <td style="vertical-align:middle;">
                <audio controls class="audio">
                <source src="{{ 'http://web.oei.bo/dclo_public/audios/'.App\Http\Controllers\TestController::audio($item->id_word)}}" type="audio/mp3">
                Your browser does not support the audio element.
                </audio>
                <br>
            </td>
            <td style="vertical-align:middle;">{{$item->language_name}}</td>
            <td style="vertical-align:middle;">
                <a class="btn btn-success" href="../{!! $item->id_word !!}&/edit" role="button"><i class="fa fa-pencil-square-o">Editar</i></a>
	        </td>
        </tr>
        @endforeach
        {{ $word->links() }}
        </tbody>
    </table>
</div>
@endsection()
