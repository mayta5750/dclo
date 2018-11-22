<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class word extends Model
{
    public function cate()
    {
        return $this->belongsTo(generic_judgments::class,'id_word');
        /*$wor=DB::table('generic_judgments')
        ->join('word', 'word.id_generic_judgment', '=', 'generic_judgments.id_generic_judgment')
        ->select('generic_judgments.id_generic_judgment','word.id_word')
        ->get();
        return view('word.index',compact('wor'));*/
       // $wor = App\word::all();
        //return view('word.index',compact('wor'));
       // $wor = App\word::where('id_word',1)->get();
       // $war = App\generic_judgments::orderBy('id_generic_judgment')->get();
       // return view('word.index', ['wor' => $wor,'war' => $war]);
    }
    protected $fillable = [
        'id_word','id_languaje_from','id_generic_judgment' ,'grapheme', 'cat_dialectal_variation', 'cat_word_origin', 'cat_word_status', 
        'get_audio_priority','id_external','created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by','deleted_at'
    ];
}
