@extends('layouts.app')
@section('content')
<div id="borde" class="borde" style="padding:80px 100px 0 100px">
<h1>Listado de Lotes</h1>
    <hr/>
    @if(Session::has('message'))
    <div class="alert-custom">
        <p>{!! Session('message') !!}</p>
    </div>
    @endif()
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>FECHA DEL LOTE</th>
            <th>CANTIDAD DE PALABRAS</th>
            <th>IDIOMA</th>
            <th width="110px;">DETALLES</th>
        </tr>
        </thead>
        <tbody>
        @foreach($lotes as $lote)
        <tr>    
            <td>
                <strong>{{$lote->created_at->formatLocalized('%d - %B - %Y %H:%I:%S')}}</strong> <i>({{$lote->created_at->diffForHumans()}})</i>
            </td>
            <td>
                {{App\Http\Controllers\TestController::contarPalabra($lote->created_at,$lote->id_language_from)}}
            </td>
            <td>
                {{$lote->language_name}}
            </td>
            <td>
                <a class="btn btn-danger" href="vistalote{{App\Http\Controllers\TestController::getIdLote($lote->created_at,$lote->id_language_from)}}/{{$lote->id_language_from}}" role="button"><i class="fa fa-trash-o">Detalle</i></a>
  		    </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection()
