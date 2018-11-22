
@extends('layouts.app')

@section('content')
<div id="borde" class="borde" style="padding:80px 100px 0 100px">
<div class="row">
    <div class = "col-md-6">
        <h1>Lote de Idiomas</h1>
        <h1>{!! $idioma !!}</h1>
        <h4><strong>{{$fecha->formatLocalized('%d de %B del %Y %H:%I:%S')}}</strong></h4>
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
    <!--<a class="btn btn-primary" href="wordcreate" style="margin-bottom: 15px;">Create New</a>-->

    @if(Session::has('message'))
    <div class="alert-custom">
        <p>{!! Session('message') !!}</p>
    </div>
    @endif()
    <table class="table table-bordered">
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
            <td>{{$item->id_word}}</td>
                <td>{{$item->grapheme}}</td>
                <td>{{App\Http\Controllers\TestController::castellano($item->id_word_definition)}}</td>
                <td><img style="width:90px;height: 90px;"src="{{$item->path==null? asset('storage/dclo_public/micelanea/nada.svg') : asset('storage/dclo_public/img_out_L/'.$item->path) }}" class="img-responsive img-rounded" /></td>
                <td>{{$item->meaning}}</td> 
               <!-- <td>{{$item->cat_category}}</td>-->   
                <td>
                    <audio controls class="audio"> 
                    <!--<source src="{!! asset('assets/'.$item->path) !!}" type="audio/mp3">-->
                    <source src="{{ asset('storage/dclo_public/audios/'.App\Http\Controllers\TestController::audio($item->id_word)) }}" type="audio/mp3">
                    Your browser does not support the audio element.
                    </audio> <br>
                   <!-- {{App\Http\Controllers\TestController::audio($item->id_word)}}-->
                                      
                </td>
                <td>{{$item->language_name}}</td>
                <td>
                <a class="btn btn-success" href="{!! $item->id_word !!}&/edit" role="button"><i class="fa fa-pencil-square-o">Editar</i></a>
                    <!--<a class="btn btn-danger" href="{{route('words.edit', $item->id_word)}}" onclick="return confirm('Quiere borrar el registro?')" role="button"><i class="fa fa-trash-o"></i></a>-->
  		        </td>
            </tr>
        @endforeach
        {{ $word->links() }}


        </tbody>
    </table>
</div>
@endsection()
