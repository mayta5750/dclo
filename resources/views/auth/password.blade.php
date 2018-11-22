@extends('layouts.app')

@section('content')
<div class="container" style="padding-top:8%">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Actualizar Contraseña') }}</div>

                <div class="card-body">
                <form method="post" action="{{url('updatepassword')}}">
 {{csrf_field()}}
 <div class="form-group row">
  <label for="mypassword" class="col-md-4 col-form-label text-md-right">Contraseña Actual:</label>
  <div class="col-md-6">
  <input type="password" name="mypassword" class="form-control">
  <div class="text-danger">{{$errors->first('mypassword')}}</div>
 </div>
 </div>

 <div class="form-group row">
  <label for="password" class="col-md-4 col-form-label text-md-right">Nueva Contraseña:</label>
  <div class="col-md-6">
  <input type="password" name="password" class="form-control">
  <div class="text-danger">{{$errors->first('password')}}</div>
 </div>
 </div>
 <div class="form-group row">
  <label for="mypassword" class="col-md-4 col-form-label text-md-right">Confirmar Contraseña:</label>
  <div class="col-md-6">
  <input type="password" name="password_confirmation" class="form-control">
 </div>
 </div>
 <div class="col-md-6 offset-md-4">

 <button type="submit" class="btn btn-primary">Actualizar</button>
 </div>
</form>

                    

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
