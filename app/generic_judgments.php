<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class generic_judgments extends Model
{
  
  protected $table = 'generic_judgments';
  protected $fillable = [
        'id_generic_judgment',
        'cat_data_type',
        'cat_state', 
        'obs', 
        'created_by', 
        'created_at', 
        'updated_by', 
        'updated_at', 
        'deleted_by',
        'deleted_at'
    ];
    public function generic_judgments()
    {
        
       // return App\word::all();;
    }
    public function Words()
    {
      return $this->has_many(words::class);
    }
    public function permissons()
    {
        return $this->belongsToMany(word::class, 'id_word');
    }
}
