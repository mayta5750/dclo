@extends('templates.master')

@section('content')

    <h2>Crear Generic Judgments</h2>
    <hr/>
<form method="POST" action="{{url('generic_judgments')}}">
    {{csrf_field()}}

    <div class="form-group">
    <label for="exampleInputEmail1">cat_data_type</label>
    <input type="text" name="cat_data_type" class="form-control" id="cat_data_type exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
  </div>

    <div class="form-group">
    <label for="exampleInputEmail1">cat_state</label>
    <input type="text" name="cat_state" class="form-control" id="cat_state exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
  </div>

    <div class="form-group">
    <label for="exampleInputEmail1">obs</label>
    <input type="text" name="obs" class="form-control" id="obs exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
  </div>

    <div class="form-group">
    <label for="exampleInputEmail1">created_by</label>
    <input type="text" name="created_by" class="form-control" id="created_by exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
  </div>

    <div class="form-group">
    <label for="exampleInputEmail1">created_at</label>
    <input type="text" name="created_at" class="form-control" id="created_at exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
  </div>

    <div class="form-group">
    <label for="exampleInputEmail1">updated_by</label>
    <input type="text" name="updated_by" class="form-control" id="updated_by exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
  </div>

    <div class="form-group">
    <label for="exampleInputEmail1">ypdated_at</label>
    <input type="text" name="updated_at" class="form-control" id="updated_at exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
  </div>

      <div class="form-group">
    <label for="exampleInputEmail1">deleted_by</label>
    <input type="text" name="deleted_by" class="form-control" id="deleted_by exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
  </div>

    <div class="form-group">
    <label for="exampleInputEmail1">deleted_at</label>
    <input type="text" name="deleted_at" class="form-control" id="deleted_at exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
  </div>
  
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

 
@endsection()