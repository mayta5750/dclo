@extends('layouts.app')
@section('content')


<div id="borde" class="borde" style="padding:80px 100px 0 100px">
<h2><strong>{{$mensaje}}</strong></h2>
<i>NOTA: no se eliminará idiomas utilizadas</i>

<table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th style="padding-left: 15px;">#</th>
            <th>IDIOMA</th>
            <th width="110px;">BORRAR</th>
        </tr>
        </thead>
        <tbody>
        @foreach($languages as $item)
            <tr>
                <th style="vertical-align:middle;">{{$item->id_language}}</th>
                <td style="vertical-align:middle;">{{$item->language_name}}</td>
                <td style="vertical-align:middle;">
                    <a class="btn btn-danger" href="{{url('destroy'.$item->id_language)}}" onclick="return confirm('Quiere borrar el registro?')" role="button"><i class="fa fa-trash-o">Eliminar</i></a>
  		        </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection()
