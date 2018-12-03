<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use generic_judgments;
use word_definitions;
use definition_categories;
use definition_meanings;
use files;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use function Psy\debug;
use App\Traits\DatesTranslator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Date\Date;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

//use App\'generic_judgments;

use DB;
class WordsController extends Controller
{
    public function getSubmiteAtAttribute($submited_at){
        return new Date($submited_at);
    }
    public function __construct()
    {
        setlocale(LC_ALL, 'es_ES');
        Carbon::setLocale('es');
        Date::setLocale('es');
        \Date::setLocale('es');

        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $a=$request->get('idioma');
        $grapheme  = $request->get('grapheme');
        $id_language_from  = $request->get('id_language_from');
        $id_language_from = $this->getId_Language($request->get('id_language_from'));

        $word = App\words::distinct()
        ->select(
        'words.id_word',
        'words.grapheme',
        'words.id_language_from',
        'generic_judgments.obs',
        'generic_judgments.cat_data_type',
        'word_definitions.key_words',
        'definition_categories.cat_category',
        'definition_meanings.meaning',
        'word2definitions.id_word_definition',
        'language_name',
        'files.path'
        )
            ->leftjoin('languages','words.id_language_from','=','languages.id_language')
            ->leftjoin('word2definitions','words.id_word','=','word2definitions.id_word')
            ->leftjoin('word_definitions','word2definitions.id_word_definition','=','word_definitions.id_word_definition')
            ->leftjoin('definition_categories','word_definitions.id_word_definition','=','definition_categories.id_word_definition')
            ->leftjoin('definition_meanings','word_definitions.id_word_definition','=','definition_meanings.id_word_definition')
            ->leftjoin('generic_judgments','definition_meanings.id_generic_judgment','=','generic_judgments.id_generic_judgment')
            //->where('words.id_generic_judgment','=','generic_judgments.id_generic_judgment')
            ->leftjoin('definition_medias','word_definitions.id_word_definition','=','definition_medias.id_word_definition')
            ->leftjoin('files','definition_medias.id_file','=','files.id_file')
           // ->where('definition_medias.id_generic_judgment','=','generic_judgments.id_generic_judgment')
            ->where('words.id_word','!=',0)
            ->where('words.id_language_from','!=',1)


            ->orderBy('id_word')
            ->grapheme($grapheme)
            ->id_language_from($id_language_from) 
            ->paginate(10);
             //->get();

            $item_languages = App\languages::distinct()
            ->select('languages.language_name')
            ->where('languages.id_language','!=',1)
            ->get();

            
        return view('words.index',compact('word','item_languages'));
    }   
    public static function castellano($id){
        $word = App\words::distinct()
            ->select('words.grapheme')
            ->join('word2definitions','words.id_word','=','word2definitions.id_word')
            ->where('word2definitions.id_word_definition','=',$id)
            ->where('words.id_language_from','=',1)
            //->with('grapheme',$grapheme)
            ->get()
            ->first();
            //->getQuery();
            //compact('word');
           $cad = $word['grapheme'];
        return $cad;        
    }
    public function getList()
    {
      $albums = Album::with('Photos')->get();
      return View::make('index')
      ->with('albums',$albums);
    }
    public function getAlbum($id)
    {
      $album = Album::with('Photos')->find($id);
      return View::make('album')
      ->with('album',$album);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*request()->validate([
          
            'grapheme' => 'required'
        ]);*/
        $cover = $request->file('filename');
        $extension = $cover->getClientOriginalExtension();
        Storage::disk('public')->put($cover->getFilename().'.'.$extension,  File::get($cover));
    
        /*$book = new App\words();
        $book->grapheme = $request->grapheme;
        //$book->mime = $cover->getClientMimeType();
        $book->original_filename = $cover->getClientOriginalName();
        $book->filename = $cover->getFilename().'.'.$extension;
        $book->save();*/
    
      //  return redirect()->route('words.inde')
       // ->with('success','Book added successfully...');

       return redirect('/words');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //$item = App\words::where('id_word','=',$id)
        $item = App\words::distinct()
        ->select(
            'words.*',
            'languages.language_name','languages.description as description_language',
            'word_definitions.key_words',
            'definition_categories.cat_category',
            'definition_meanings.meaning',
            'generic_judgments.obs',
            'files.path',
            'word2definitions.comments',
            'word2definitions.id_word_definition'
            )
            ->where('words.id_word','=',$id)
            ->leftjoin('languages','words.id_language_from','=','languages.id_language')
            ->leftjoin('word2definitions','words.id_word','=','word2definitions.id_word')
            ->leftjoin('word_definitions','word2definitions.id_word_definition','=','word_definitions.id_word_definition')
            ->leftjoin('definition_categories','word_definitions.id_word_definition','=','definition_categories.id_word_definition')
            ->leftjoin('definition_meanings','word_definitions.id_word_definition','=','definition_meanings.id_word_definition')
            ->leftjoin('generic_judgments','definition_meanings.id_generic_judgment','=','generic_judgments.id_generic_judgment')
            //->where('words.id_generic_judgment','=','generic_judgments.id_generic_judgment')
            ->leftjoin('definition_medias','word_definitions.id_word_definition','=','definition_medias.id_word_definition')
            ->leftjoin('files','definition_medias.id_file','=','files.id_file')
            ->leftjoin('word_audios','words.id_word','=','word_audios.id_word')
            //->where('word_audios.id_file','=','files.id_file')
            //join('')
            ->get()
            ->first();
            $items = App\words::distinct()
            ->select('files.path')
            ->where('words.id_word','=',$id)
            ->leftjoin('word_audios','words.id_word','=','word_audios.id_word')
            ->leftjoin('files','word_audios.id_file','=','files.id_file')
            ->get()
            ->first();
            $item_languages = App\languages::distinct()
            ->select('languages.language_name')
            ->where('languages.id_language','!=',1)
            ->get();
            
            
        return view('words.edit',compact('item','items','item_languages'));
    }
   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $idioma=$this->getId_Language($request->get('idioma'));
        $item = App\words::distinct()
        
            ->join('languages','words.id_language_from','=','languages.id_language')
            ->join('word2definitions','words.id_word','=','word2definitions.id_word')
            ->join('word_definitions','word2definitions.id_word_definition','=','word_definitions.id_word_definition')
            ->join('definition_categories','word_definitions.id_word_definition','=','definition_categories.id_word_definition')
            ->join('definition_meanings','word_definitions.id_word_definition','=','definition_meanings.id_word_definition')
            ->join('generic_judgments','definition_meanings.id_generic_judgment','=','generic_judgments.id_generic_judgment')
           // ->where('words.id_generic_judgment','=','generic_judgments.id_generic_judgment')//ojo
            ->join('definition_medias','word_definitions.id_word_definition','=','definition_medias.id_word_definition')
            ->leftjoin('files','definition_medias.id_file','=','files.id_file')
            //->leftjoin('word_audios','words.id_word','=','word_audios.id_word')
            ->where('words.id_word','=',$id)
            ->getQuery();
        
        $item->update(['grapheme' => $request->grapheme,
                        //'language_name' => $request->language_name,
                        'id_language_from' => $idioma,
                        'key_words' => $request->key_words,
                        'cat_category' => $request->cat_category,
                        'meaning' => $request->meaning,
                        //'cat_data_type' => $request->cat_data_type,
                        'obs' => $request->obs,
                        'comments' => $request->comments
                        //'path' => $request->path
        ]);
        if($request->hasFile('filename')){
            $cover = $request->file('filename');
            $extension = $cover->getClientOriginalExtension();
            $nom = $cover->getClientOriginalName();
            if($cover->getClientOriginalExtension()!="svg"){
                echo'<script type="text/javascript">
                alert("Seleccione un Archivo .SVG");
                </script>';
            }
            //dd($this->get_ruta_imagen($id));
            if(!$this->get_ruta_imagen($id)){
                //dd("ddd");
                /* ================================================= */

               // dd($this->getIdGenericJudgments()+1);
                    $generic_judgments = new App\generic_judgments();
                    $generic_judgments->id_generic_judgment = $this->getIdGenericJudgments()+1;
                    $generic_judgments->cat_data_type = 'WMEDIA';
                    $generic_judgments->cat_state = 'ACCPT';
                    $generic_judgments->obs = 'VOCABULARIO-PEDAGÓGICO';
                    $generic_judgments->created_by = 0;
                    $generic_judgments->created_at = $this->getFechaLote($id);
                    $generic_judgments->updated_by = 0;
                    $generic_judgments->updated_at = $this->getFechaLote($id);
                    $generic_judgments->deleted_by = null;
                    $generic_judgments->deleted_at = null;
                    $generic_judgments->save();
                /* ================================================= */
                    $rut = $this->getIdRuta($id);
                    $files = new App\files();
                    $files->id_file = $this->getIdFiles()+1;
                    $files->description = 'descripcion imagen - ' . $id . '.svg';;
                    $files->mime_type = 'image/svg';
                    $files->cat_file_type = 'IMAGE';
                    $files->cat_storage_type = 'vps';
                    $files->path = $rut.$id.'.svg';//$nueva_carpeta."/".strval($id_WS).".svg";
                    $files->cat_licence_type = 'NONE';
                    $files->author_name = 'author-name';
                    $files->url_origin = 'https://goo.gl/u5H65S';
                    $files->created_by = 0;
                    $files->created_at = $this->getFechaLote($id);
                    $files->updated_by = 0;
                    $files->updated_at = $this->getFechaLote($id);
                    $files->deleted_by = null;
                    $files->deleted_at = null;
                    $files->save();
                    Storage::disk('public')->put('img_out_L/'.$rut.$id.'.svg',  File::get($cover));
                /* ================================================= */
                   $definition_medias = new App\definition_medias();
                    $definition_medias->id_definition_media = $this->getIdDefinitionMedias()+1;
                    $definition_medias->id_file = $this->getIdFiles();
                    $definition_medias->id_word_definition = $this->getIdWD($id);
                    $definition_medias->id_generic_judgment = $this->getIdGenericJudgments();
                    $definition_medias->cat_def_media_type = 'IMG';
                    $definition_medias->cat_media_origin = 'OFL';
                    $definition_medias->default_resource = 1;
                    $definition_medias->relevance = 70;
                    $definition_medias->id_external = null;
                    $definition_medias->cat_status = 'OFL';
                    $definition_medias->created_by = 0;
                    $definition_medias->created_at = $this->getFechaLote($id);
                    $definition_medias->updated_by = 0;
                    $definition_medias->updated_at = $this->getFechaLote($id);
                    $definition_medias->deleted_by = null;
                    $definition_medias->deleted_at = null;
                    $definition_medias->save();
            }else{
                $ruta_imagen = $this->get_ruta_imagen($id);
                $cadena = (explode("/",$ruta_imagen));
                $ruta='';
                for($i=0;$i<sizeof($cadena)-1;++$i)
                    $ruta = $ruta.$cadena[$i].'/';
                Storage::disk('public')->delete('img_out_L/'.$ruta_imagen);
                Storage::disk('public')->put('img_out_L/'.$ruta.$id.'_'.date("d-m-Y").'.svg',  File::get($cover));
                $item->update(['path' => $ruta.$id.'_'.date("d-m-Y").'.svg']);
            }
            
        }
        if($request->hasFile('fileaudio')){
            $item = App\words::distinct()
            ->join('word_audios','words.id_word','=','word_audios.id_word')
            ->join('files','word_audios.id_file','=','files.id_file')
            ->where('words.id_word','=',$id)
            ->getQuery();
            $cover = $request->file('fileaudio');
            $extension = $cover->getClientOriginalExtension();
            if($cover->getClientOriginalExtension()!="mp3"){
                echo'<script type="text/javascript">
                alert("Seleccione un Archivo .MP3");
                </script>';
            }
            
            $ruta_audio = $this->get_ruta_audio($id);
            $cadena = (explode("/",$ruta_audio));
            $ruta='';
            for($i=0;$i<sizeof($cadena)-1;++$i)
                $ruta = $ruta.$cadena[$i].'/';
            Storage::disk('public')->delete('audios/'.$ruta_audio);
            Storage::disk('public')->put('audios/'.$ruta.$id.'_'.date("d-m-Y").'.mp3',  File::get($cover));
            $item->update(['path' => $ruta.$id.'_'.date("d-m-Y").'.mp3']);
        }
       // if($request->get('words.id_language_from')==1){
            $castellano_id = $this->castellano_id($id);
            $item = App\words::distinct()
            ->join('word2definitions','words.id_word','=','word2definitions.id_word')
            ->where('word2definitions.id_word_definition','=',$castellano_id)
            ->where('words.id_language_from','=',1)
            ->getQuery();
            $item->update(['grapheme' => $request->get('castellano')]);
       // }
       if(strpos(redirect()->getUrlGenerator()->previous(),$id.'&'))
            return redirect('/words/vistalote'.$id);
       else
            return redirect('/words');
    
    }
    public static function audio($id_word){
        $word = App\words::distinct()
            ->select('files.path')
            ->where('words.id_word','=',$id_word)
            ->join('word_audios','words.id_word','=','word_audios.id_word')
            ->join('files','word_audios.id_file','=','files.id_file')
            ->get()
            ->first();
           $cad = $word['path'];
        return $cad;        
    }
    public static function imagen($id_word){
        $word = App\words::distinct()
            ->select('files.path')
            ->where('words.id_word','=',$id_word)
            ->join('word2definitions','words.id_word','=','word2definitions.id_word')
            ->join('word_definitions','word2definitions.id_word_definition','=','word_definitions.id_word_definition')
            ->join('definition_medias','word_definitions.id_word_definition','=','definition_medias.id_word_definition')
            ->join('files','definition_medias.id_file','=','files.id_file')
            ->get()
            ->first();
           $cad = $word['path'];
        return $cad;        
    }
    public static function castellano_id($id){
        $item = App\words::distinct()
        ->select('word_definitions.id_word_definition')
            ->join('word2definitions','words.id_word','=','word2definitions.id_word')
            ->join('word_definitions','word2definitions.id_word_definition','=','word_definitions.id_word_definition')
            ->where('words.id_word','=',$id)
            ->get()
            ->first();
           $cad = $item['id_word_definition'];
        return $cad;        
    }
    public function getId_Language($language_name){
        $language_name = strtoupper($language_name);
        //dd($language_name);
        $languages = App\languages::distinct()
            ->select('languages.id_language')
            ->where('languages.language_name','=',$language_name)
            ->get()
            ->first();
           $id_language = $languages['id_language'];
        //   dd($id_language);
        return $id_language;  
    }
    public function get_ruta_audio($id){
        $word = App\words::distinct()
            ->select('files.path')
            ->where('words.id_word','=',$id)
            ->leftjoin('word_audios','words.id_word','=','word_audios.id_word')
            ->leftjoin('files','word_audios.id_file','=','files.id_file')
            ->get()
            ->first();
       $cad = $word['path'];
      // dd($cad);
    return $cad;     
    }
    public function get_ruta_imagen($id){
        $word = App\words::distinct()
            ->select('files.path')
            ->where('words.id_word','=',$id)
            ->leftjoin('languages','words.id_language_from','=','languages.id_language')
            ->leftjoin('word2definitions','words.id_word','=','word2definitions.id_word')
            ->leftjoin('word_definitions','word2definitions.id_word_definition','=','word_definitions.id_word_definition')
            ->leftjoin('definition_categories','word_definitions.id_word_definition','=','definition_categories.id_word_definition')
            ->leftjoin('definition_meanings','word_definitions.id_word_definition','=','definition_meanings.id_word_definition')
            ->leftjoin('generic_judgments','definition_meanings.id_generic_judgment','=','generic_judgments.id_generic_judgment')
            //->where('words.id_generic_judgment','=','generic_judgments.id_generic_judgment')
            ->leftjoin('definition_medias','word_definitions.id_word_definition','=','definition_medias.id_word_definition')
            ->leftjoin('files','definition_medias.id_file','=','files.id_file')
            ->get()
            ->first();
            //->getQuery();
            //compact('word');
           $cad = $word['path'];
          // dd($cad);
        return $cad;  
    }
    public function getIdFiles(){
        $word = App\files::distinct()
        ->orderby('id_file','DESC')->take(1)->get()->first();
        $id = $word['id_file'];
        return ($id);
    }
    public function getIdGenericJudgments(){
        $word = App\generic_judgments::distinct()
        ->orderby('id_generic_judgment','DESC')->take(1)->get()->first();
        $id = $word['id_generic_judgment'];
        return ($id);
    }
    public function getIdDefinitionMedias(){
        $word = App\definition_medias::distinct()
        ->orderby('id_definition_media','DESC')->take(1)->get()->first();
        $id = $word['id_definition_media'];
        return ($id);
    }
    public function getIdRuta($id){
        $word = App\words::distinct()
            ->select('files.path')
            ->where('words.id_word','=',$id)
            ->join('word_audios','words.id_word','=','word_audios.id_word')
            ->join('files','word_audios.id_file','=','files.id_file')
            ->get()
            ->first();
            $cad = $word['path'];
            $cadena = (explode("/",$cad));
            $ruta='';
            for($i=0;$i<sizeof($cadena)-1;++$i){
                $ruta = $ruta.$cadena[$i].'/';
            }
          //dd($ruta);
        return $ruta;
    } 
    public function getFechaLote($id){
        $word = App\words::distinct()
            ->select('words.created_at')
            ->where('words.id_word','=',$id)
            ->get()
            ->first();
            $fecha = $word['created_at'];
        return $fecha;
    } 
    public function getIdWD($id){
        $word = App\words::distinct()
            ->select('word_definitions.id_word_definition')
            ->where('words.id_word','=',$id)
            ->leftjoin('word2definitions','words.id_word','=','word2definitions.id_word')
            ->leftjoin('word_definitions','word2definitions.id_word_definition','=','word_definitions.id_word_definition')
            ->get()
            ->first();
           $cad = $word['id_word_definition'];
         //dd($cad);
        return $cad;  
    }
}
