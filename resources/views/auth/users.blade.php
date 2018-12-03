@extends('layouts.app')
@section('content')
<div id="borde" class="borde" style="padding:80px 100px 0 100px">

<table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th style="padding-left: 15px;">#</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Tipo de Usuario</th>
          <!--  <th width="110px;">BORRAR</th>-->
        </tr>
        </thead>
        <tbody>
        @foreach($users as $item)
            <tr>
                <th style="vertical-align:middle;">{{$item->id}}</th>
                <td style="vertical-align:middle;">{{$item->name}}</td>
                <td style="vertical-align:middle;">{{$item->email}}</td>
                <td style="vertical-align:middle;">{{$item->tipoUsuario==1? 'ADMINISTRADOR' : 'USUARIO' }}</td>


               <!-- <td style="vertical-align:middle;">
                    <a class="btn btn-danger" href="{{url('destroy'.$item->id_language)}}" onclick="return confirm('Quiere borrar el registro?')" role="button"><i class="fa fa-trash-o">Eliminar</i></a>
  		        </td>-->
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection()
