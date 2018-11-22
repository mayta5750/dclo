
@extends('layouts.app')

@section('content')
<div id="borde" class="borde" style="padding:30px 100px 0 100px">
<?php 
           if(Auth::user()->tipoUsuario==1){?> @include('words.menu_admin')<?php ; } 
           if(Auth::user()->tipoUsuario!=1){?> @include('words.menu_user')<?php ; }     
           //if($usuario->tipoUsuario!=1){ include('menus/submenu_standard.php'); }    
?>
    <hr/>
    <!--<a class="btn btn-primary" href="wordcreate" style="margin-bottom: 15px;">Create New</a>-->
    @if(Session::has('message'))
    <div class="alert-custom">
        <p>{!! Session('message') !!}</p>
    </div>
    @endif()
    <div class="page-header">
    {!! Form::open(['id_word' => 'dataForm', 'method' => 'GET', 'url' => '/words/']) !!}

        <div class="row">
            <div class="col">
                <label><h4>Busquedar Palabra</h4></label>
                 <div class="form-group">
                    {{ Form::text('grapheme', null, ['class' => 'form-control', 'placeholder' => 'Nombre']) }}
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-outline-info">
                        <span class="glyphicon glyphicon-search">BUSCAR</span>
                    </button>
                </div>          
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="id_language_from"><h4>Seleccionar Idioma</h4></label>
                    <select class="form-control" id="id_language_from" name="id_language_from">
                    <option>TODOS</option>
                    @foreach($item_languages as $language)
                        <option>{{$language -> language_name}}</option>
                    @endforeach

                    </select>
                </div>
            </div>
        </div>
    {{ Form::close() }}
                    
  
    
</div>







<!-- PASA POR VAR UNA VARIABLE A WORDSCONTROLLER

    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search"name="var">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
-->
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
                <td>{{App\Http\Controllers\WordsController::castellano($item->id_word_definition)}}</td>
                <!--<td><img style="width:90px;height: 90px;"src="{{$item->path==null? asset('storage/dclo_public/micelanea/nada.svg') : asset('storage/dclo_public/img_out_L/'.$item->path) }}" class="img-responsive img-rounded" /></td>-->
                <td><img style="width:90px;height: 90px;"src="{{$item->path==null? asset('storage/dclo_public/micelanea/nada.svg') : 'http://web.oei.bo/dclo_public/img_out_L/L_'.$item->path }}" class="img-responsive img-rounded img" /></td>

                <td>{{$item->meaning}}</td>
                       <!-- <option>{{1==1? 'f' : '' }}</option>  -->

                
               <!-- <td>{{$item->cat_category}}</td>-->   
                <td>
                    <audio controls class="audio"> 
                    <!--<source src="{!! asset('assets/'.$item->path) !!}" type="audio/mp3">-->
                    <!--<source src="{{ asset('storage/dclo_public/audios/'.App\Http\Controllers\TestController::audio($item->id_word)) }}" type="audio/mp3">-->
                    <source src="{{ 'http://web.oei.bo/dclo_public/audios/'.App\Http\Controllers\TestController::audio($item->id_word) }}" type="audio/mp3">

                    Your browser does not support the audio element.
                    </audio> <br>
                    <!--{{App\Http\Controllers\WordsController::audio($item->id_word)}}-->
                                      
                </td>
                <td>{{$item->language_name}}</td>
                <td>
                    <a class="btn btn-success" href="words/{!! $item->id_word !!}/edit" role="button"><i class="fa fa-pencil-square-o">Editar</i></a>
                    <!--<a class="btn btn-danger" href="{{route('words.edit', $item->id_word)}}" onclick="return confirm('Quiere borrar el registro?')" role="button"><i class="fa fa-trash-o"></i></a>-->
  		        </td>
            </tr>

        @endforeach
        {{ $word->links() }}

        </tbody>
    </table>
</div>
@endsection()
