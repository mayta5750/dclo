<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class word_definitions extends Model
{
    protected $table = 'word_definitions';
    protected $fillable = [
         'id_word_definition', 'key_words'
      ];
    public function word_definitions()
    {
        
        return App\word_definitions::all();;
    }
}
