@extends('layouts.app')
   @if(Session::has('message'))
    <div class="alert-custom">
        <p>{!! Session('message') !!}</p>
    </div>
    @endif()
@section('content')
<div id="borde" class="borde" style="padding:80px 100px 0 100px">
<div class="" style="padding:0 10% 0 10%">

    <h2>Actualizar Registro</h2>
        <hr/>
        
        {!! Form::open(['id_word' => 'dataForm', 'method' => 'PATCH', 'url' => '/words/' . $item->id_word, 'enctype' => 'multipart/form-data' ]) !!}
        <!-- {!! Form::open(['url' => '/words', 'enctype' => 'multipart/form-data']) !!}-->
        <!--AUDIO-->

        <div class="row">
            <div class="col-md-4">
                <div>
                        <img style="width:100%"src="{{ asset('storage/dclo_public/img_out_L/'.$item->path) }}" class="img-responsive img-rounded" />
                        <!--ln -s /opt/lampp/htdocs/oei/storage/app/public/imagen /opt/lampp/htdocs/oei/public/assets/imagen-->              
                </div>
                <div>
                        <audio controls class="audio2"> 
                        <!--<source src="{!! asset('assets/'.$item->path) !!}" type="audio/mp3">-->
                        <source src="{{ asset('storage/dclo_public/audios/'.$items->path) }}" type="audio/mp3">
                        Your browser does not support the audio element.
                        </audio> <br>                         
                </div>
            </div>
            <div class="col-md-8" >
            <!--WORDS-->
            <div class="form-group" style="padding:0 0 2% 10%">
                    {!! Form::label('id_word', 'ID : ',array('class' => 'label')); !!}
                    {!! Form::label('id_word', $item->id_word,array('class' => 'label')); !!}
                </div> 
            {{ csrf_field() }}
            <hr/>
                <div class="form-group" style="padding:0 0 2% 10%">
                    <label for="filename" class="label">IMAGEN:</label>
                    <input type="file" class="form-control-file" id="exampleFormControlFile1" name="filename"/>
                </div>
                <hr/>
                <div class="form-group" style="padding:0 0 2% 10%">
                    <label for="fileaudio" class="label">AUDIO:</label>
                    <input type="file" class="form-control-file" id="exampleFormControlFile1" name="fileaudio"/>
                </div>

                <hr/>
                


            </div>
        </div>
        <hr/>

                    <!--Language-->
                <div class="form-group">
                    <label for="idioma" class="label">Idioma :</label>
                    <select class="form-control" id="idioma" name="idioma">
                        <option>{{ $item->language_name }}</option>
                        <!--<option>CASTELLANO</option>-->
                        @foreach($item_languages as $language)
                        <option>{{$language -> language_name}}</option>
                        @endforeach
                       <!-- <option>{{1==1? 'f' : '' }}</option>  -->
                    </select>
                </div>

                <div class="row">
                    <div class="col">
                    <!--WORDS-->
                    <div class="form-group">
                        {!! Form::label('grapheme', 'Palabra o Frase en '.$item->language_name.' :',array('class' => 'label')); !!}
                        {!! Form::text('grapheme', $item->grapheme , ['placeholder' => '', 'class' => 'form-control']); !!}
                    </div>
                    </div>
                    <div class="col">
                    <!--WORDS castellano-->
                    <div class="form-group">
                        {!! Form::label('castellano', 'Castellano :',array('class' => 'label')); !!}
                        {!! Form::text('castellano', App\Http\Controllers\WordsController::castellano($item->id_word_definition) , ['placeholder' => '', 'class' => 'form-control']); !!}
                    </div>
                    </div>
                </div>
                <!--DEFINITION MEANINGS-->
                <div class="form-group">
                    {!! Form::label('meaning', 'Definición :',array('class' => 'label')); !!}
                    {!! Form::text('meaning', $item->meaning , ['placeholder' => '', 'class' => 'form-control']); !!}
                </div>
                <!--WORD_DEFINITIONS-->
                <div class="form-group">
                    {!! Form::label('key_words', 'Palabras Clave :',array('class' => 'label')); !!}
                    {!! Form::text('key_words', $item->key_words , ['placeholder' => '', 'class' => 'form-control']); !!}
                </div>
                <!--DEFINITION CATEGORIES-->
                <div class="form-group">
                    {!! Form::label('cat_category', 'Categoria :',array('class' => 'label')); !!}
                    {!! Form::text('cat_category', $item->cat_category , ['placeholder' => '', 'class' => 'form-control']); !!}
                </div>

 
    <div class="row">
        <div class="col">
            <!--GENERIC JUDGMENTS-->
            <div class="form-group">
                {!! Form::label('obs', 'Observaciones :',array('class' => 'label')); !!}
                {!! Form::textarea('obs', $item->obs , ['placeholder' => '', 'class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col">
            <!--WORD2DEFINITIONS-->
            <div class="form-group">
                {!! Form::label('comments', 'Comentarios :',array('class' => 'label')); !!}
                {!! Form::textarea('comments', $item->comments , ['placeholder' => '', 'class' => 'form-control']); !!}
            </div>
        </div>

    </div>
    <div class="col-2 mx-auto">
        {!! Form::submit('ACTUALIZAR', ['class' => 'btn btn-primary pull-right center']); !!}
    </div>
    {!! Form::close() !!}
    </div>

</div>
    
    

    

 <br>
</div>
   

@endsection()