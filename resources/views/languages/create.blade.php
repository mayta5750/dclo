@extends('layouts.app')
   @if(Session::has('message'))
    <div class="alert-custom">
        <p>{!! Session('message') !!}</p>
    </div>
    @endif()
@section('content')
<div id="borde" class="borde" style="padding:80px 200px 0 200px">

@section('content')

    <h2>Nueva Idioma</h2>
    <hr/>
<form method="POST" action="{{url('store')}}">
    {{csrf_field()}}

    <div class="form-group row">
    <label for="exampleInputEmail1" class="col-md-4 col-form-label text-md-right">Idioma : </label>
    <input type="text" name="language_name" class="form-control col-md-6" id="cat_data_type exampleInputEmail1" aria-describedby="emailHelp" placeholder="">
  </div>
  <div class="col-md-5 offset-md-5">
    <button type="submit" class="btn btn-primary">Adicionar</button>
  </div>
</form>
</div>
 
@endsection()