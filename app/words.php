<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//namespace App\Imports;
use App\words;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
/*
use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Migrations\Migration;
*/
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
//use Illuminate\Foundation\Auth\User as Authenticatable;
use generic_judgments;
use word_definitions;
use App;
//use App\generic_judgments;
class words extends Model
{
    use Notifiable;
  /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


     /**
* Indicates if the IDs are auto-incrementing.
*
* @var bool
*/
    public $incrementing = false;

    protected $table = 'words';
    protected $fillable = [
        'id_word',
        'id_language_from', 
        'id_generic_judgment',
        'grapheme', 
        'cat_dialectal_variation', 
        'cat_word_origin', 
        'cat_word_status', 
        'get_audio_priority', 
        'id_external', 
        'cat_status', 
        'created_by', 
        'created_at', 
        'updated_by', 
        'updated_at', 
        'deleted_by', 
        'deleted_at'

    ];
 

   // protected $fillable = array('');

    
    public function scopeGrapheme($query,$grapheme){
        if($grapheme)
            return $query->where('grapheme','LIKE',"%$grapheme%");
    }
    public function scopeId_language_from($query,$id_language_from){
        if($id_language_from)
            return $query->where('id_language_from','LIKE',"%$id_language_from%");
    }
    public function rules(){

    }
    public function profile(){
        return View('words.profile');
    }

    public function hola(){
        
        return ('print(1)');
    }
    public function up()

    {

        Schema::create('words', function (Blueprint $table) {

            $table->increments('id_word');

            $table->string('grapheme');

           // $table->text('body');

           // $table->timestamps();

        });

    }



    /**

     * Reverse the migrations.

     *

     * @return void

     */

    public function down()

    {

        Schema::dropIfExists('products');

    }

  /*
    public function Words()
    {
      return $this->has_many(words::class);
    }
    public function permissions()
    {
        return $this->all(words::class, 'id_word');
    }

    public function gener()
    {
        return $this->belongsTo(App\generic_judgments::class, 'id_word','id_generic_judgment');
    }
    public function gene(){
        return $this->belongsTo(App\generic_judgments::class, 'id_word','id_generic_judgment');
    }*/
}
