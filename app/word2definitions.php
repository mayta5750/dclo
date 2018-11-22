<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class word2definitions extends Model
{
    protected $fillable = [
        'id_word2definition','id_word','id_word_definition' ,'comments', 'cat_word_status', 
        'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by','deleted_at'
    ];
}
