@extends('layouts.app')

@section('content')
<div id="borde" class="borde" style="padding:5% 200px 0 200px">
<div class="row">
    <div class = "col">
        <h1>Importar Lote de Idiomas</h1>
    </div>
    
</div>
<hr>
<form method="POST" action="{{ url('import') }}" accept-charset="UTF-8" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group" style="padding:30px 200px 0 200px">
                <label for="archivo" class="label">Archivo ".CSV"</label>
                <input type="file" class="form-control-file" id="exampleFormControlFile1" name="archivo"/>
            </div>
           <!-- <div class="form-group" style="padding:30px 50% 0 50%">
               <p><input type="submit" class="btn btn-outline-secondary"/></p>
            </div>-->
        </div>
        <div class="col-md-12">
            <div class="form-group" style="padding:30px 200px 0 200px">
                <label for="archivo_audio" class="label">Comprimido de Audios ".ZIP"</label>
                <input type="file" class="form-control-file" id="exampleFormControlFile1" name="archivo_audio"/>
            </div>
            
        </div>
        <div class="col-md-12">
            <div class="form-group" style="padding:30px 200px 0 200px">
                <label for="archivo_imagen" class="label">Comprimido de Imagenes ".ZIP"</label>
                <input type="file" class="form-control-file" id="exampleFormControlFile1" name="archivo_imagen"/>
            </div>
            
        </div>
        <div class="form-group" style="padding:30px 50% 0 50%">
                <p><input type="submit" class="btn btn-outline-secondary"/></p>
        </div>
    </div>

</form>
    
<hr>
<h3>Recomendaciones: </h3>

@endsection()
