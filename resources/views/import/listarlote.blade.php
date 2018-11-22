
@extends('layouts.app')

@section('content')
<div id="borde" class="borde" style="padding:80px 100px 0 100px">

<h1>Listado de Lotes</h1>
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
            <th>FECHA DEL LOTE</th>
            <th width="110px;">DETALLES</th>
        </tr>
        </thead>
        <tbody>
        @foreach($lotes as $lote)
       
            <tr>    
            <td><strong>{{$lote->created_at->formatLocalized('%d de %B del %Y %H:%I:%S')}}</strong> <i>({{$lote->created_at->diffForHumans()}})</i></td>

                <td>
                <a class="btn btn-danger" href="vistalote{{App\Http\Controllers\TestController::getIdLote($lote->created_at)}}" role="button"><i class="fa fa-trash-o">Detalle</i></a>
  		        </td>
            </tr>
        @endforeach
        {{ $lotes->links() }}


        </tbody>
    </table>
</div>
@endsection()
