<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class languages extends Model
{
    protected $fillable = ['id_language','language_name', 'description', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by','deleted_at'];

}
