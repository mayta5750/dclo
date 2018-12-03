<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\word2definitions;
use App\word_audios;
use App\audio_judgments;
use App\definition_medias;
use App\files;
use App\definition_meanings;
use App\definition_categories;
use App\word_definitions;
use App\generic_judgments;
use App\words;
use Carbon\Carbon;
use Jenssegers\Date\Date;
use App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

use App\Traits\DatesTranslator;

use function Psy\debug;
use DB;
class TestController extends Controller
{
    public function __construct()
    {
        setlocale(LC_ALL, 'es_ES');
        Carbon::setLocale('es');
        Date::setLocale('es');
        \Date::setLocale('es');
    }
    public function getSubmiteAtAttribute($submited_at){
        return new Date($submited_at);
    }
    public function vistalote2(Request $request)
    {
        
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
        'files.path',
        'word2definitions.id_word_definition',
        'language_name'
        )
            ->leftjoin('languages','words.id_language_from','=','languages.id_language')
            ->leftjoin('word2definitions','words.id_word','=','word2definitions.id_word')
            ->leftjoin('word_definitions','word2definitions.id_word_definition','=','word_definitions.id_word_definition')
            ->leftjoin('definition_categories','word_definitions.id_word_definition','=','definition_categories.id_word_definition')
            ->leftjoin('definition_meanings','word_definitions.id_word_definition','=','definition_meanings.id_word_definition')
            ->leftjoin('generic_judgments','definition_meanings.id_generic_judgment','=','generic_judgments.id_generic_judgment')
         // ->where('words.id_generic_judgment','=','generic_judgments.id_generic_judgment')
            ->leftjoin('word_audios','words.id_word','=','word_audios.id_word')
            ->leftjoin('files','word_audios.id_file','=','files.id_file')
         // ->where('words.grapheme',$a)
            ->orderBy('id_word')
            ->get();

            $words = App\words::distinct()
            ->select(
            'words.grapheme as 
            castellano'
            )
                ->join('word2definitions','words.id_word','=','word2definitions.id_word')
                ->where('words.id_language_from','=',1)
                // ->where('words.grapheme',$a)
                ->get();
        return view('/import.vistalote',compact('word','words'));
    }
    public function import(Request $request)
    {
        $ruta_arch_audio = 'assets/archivos/audio/';
        $ruta_arch_image = 'assets/archivos/imagen/';
        $fecha = date("Y-m-d h:m:s");

        /*================================ audio ==================================*/
        if($request->hasFile('archivo_audio')){
            $cover_arch_audio = $request->file('archivo_audio');
            $extension_arch_audio = $cover_arch_audio->getClientOriginalExtension();
            $nom_arch_audio = $cover_arch_audio->getClientOriginalName();
            if($extension_arch_audio=="zip"){
                Storage::disk('public')->put('audios/'.$cover_arch_audio->getClientOriginalName(),  File::get($cover_arch_audio));
                $carpeta_audio=explode('.',$nom_arch_audio)[0];
                //exec("tar -xvf assets/archivos/audio/".$nom_arch_audio." --directory assets/archivos/audio/");
                exec("unzip storage/dclo_public/audios/".$nom_arch_audio." -d storage/dclo_public/audios/");
                exec("rm -R storage/dclo_public/audios/".$nom_arch_audio);
            }else{
                echo'<script type="text/javascript">
                alert("Seleccione un comprimido .ZIP");
                window.location.href="words/lote";
                </script>';
            }
        }else{
            echo'<script type="text/javascript">
                alert("No se encontró el comprimido de AUDIOS .ZIP");
                window.location.href="words/lote";
                </script>';
        }
        /*================================ imagen ==================================*/
        if($request->hasFile('archivo_imagen')){
            $cover_arch_imagen = $request->file('archivo_imagen');
            $extension_arch_imagen = $cover_arch_imagen->getClientOriginalExtension();
            $nom_arch_imagen = $cover_arch_imagen->getClientOriginalName();
            if($extension_arch_imagen=="zip"){
                Storage::disk('public')->put('img_out_L/'.$cover_arch_imagen->getClientOriginalName(),  File::get($cover_arch_imagen));
                $carpeta_imagen=explode('.',$nom_arch_imagen)[0];
                exec("unzip storage/dclo_public/img_out_L/".$nom_arch_imagen." -d storage/dclo_public/img_out_L/");
                exec("rm -R storage/dclo_public/img_out_L/".$nom_arch_imagen);
            }
        }
        /* ===================================================================================================0 */
        /*================================ archivo csv ==================================*/
        if($request->hasFile('archivo')){
            //dd($request->file('archivo'));

            $cover = $request->file('archivo');
            $extension = $cover->getClientOriginalExtension();
            $nom = $cover->getClientOriginalName();
            //dd($extension);
            if($extension=="csv"){
                Storage::disk('public')->put('csv/'.$cover->getClientOriginalName(),  File::get($cover));
            }else{
                echo'<script type="text/javascript">
                alert("Seleccione un Archivo .CSV");
                window.location.href="words/lote";
                </script>';
            }
           // $item->update(['path' => $cover->getClientOriginalName()]);
        }else{
            echo'<script type="text/javascript">
                alert("No se encontró el Archivo .CSV");
                window.location.href="words/lote";
                </script>';
        }
        $path = public_path('storage/dclo_public/csv/'.$cover->getClientOriginalName());
        $lines = file($path);
       // $utf8_lines = array_map('utf8_encode', $lines);//'utf8_encode'
       $delimitador=array();
       $array = array_map('str_getcsv',$lines);
       for($i=0;$i<sizeof($array);++$i){
           $delimitador[$i]='&';
       }
        $array = array_map('str_getcsv',$lines,$delimitador);
        //para comprobar si esta bien el deliitador
        if(isset($array[0][1])==false){
            echo'<script type="text/javascript">
            alert("Verifique que esté DELIMITADO correctamente");
            window.location.href="words/lote";
            </script>'; 
        }
        $nWords = sizeof($array)-1;

        $tam_word2definitions = $this->getIdWord2Definitions();
        $tam_word_audios = $this->getIdWordAudios();
        $tam_audio_judgments = $this->getIdAudioJ();
        $tam_definition_medias = $this->getIdDefinitionMedias();
        $tam_files = $this->getIdFiles();
        $tam_generic = $this->getIdGenericJudgments();
        $tam_words = $this->getIdTamWords();
        $tam_word_definitions = $this->getIdWordDefinitions();
        $tam_definition_categories = $this->getIdDefinitionCategories();
        $tam_definition_meanings = $this->getIdDefinitionMeanings();

        $id_language  = $this->getIdioma($array[0][2]);
        $id_DMed = $tam_definition_medias;
		$id_GJ_webmedia = $tam_generic+$nWords+$nWords+$nWords;
        $id_FL_img = $tam_files+$nWords;
        
    /* =========================== */
        $con=0;
        $con2=0;

        $con_img=0;
        $con2_img=0;
        $nueva_carpeta = substr(strtolower($array[0][2]),0,2).'_'.date("dmY-h:m:s");
    /* =========================== */




        $eliminados = '';
        $directorio = opendir('storage/dclo_public/audios/'.$carpeta_audio);
        for($i=1;$i<sizeof($array);++$i){
            //dd($this->getDuplicado($array[0][2],$array[$i][2]));
            if($i==1){
                File::makeDirectory('storage/dclo_public/audios/'.$nueva_carpeta);
                File::makeDirectory('storage/dclo_public/img_out_L/'.$nueva_carpeta);
                //File::deleteDirectory(public_path('storage/dclo_public/audios/'.$nueva_carpeta));
                //exec('mkdir storage/dclo_public/audios/'.$nueva_carpeta);
            }
            if(!File::exists('storage/dclo_public/audios/'.$carpeta_audio.'/'.$array[$i][6].'.mp3')){
                    File::deleteDirectory(public_path('storage/dclo_public/audios/'.$carpeta_audio));
                    File::deleteDirectory(public_path('storage/dclo_public/img_out_L/'.$carpeta_imagen));
                    File::deleteDirectory(public_path('storage/dclo_public/audios/'.$nueva_carpeta));
                    File::deleteDirectory(public_path('storage/dclo_public/img_out_L/'.$nueva_carpeta));
                    $this->eliminar($fecha);
                    echo'<script type="text/javascript">
                    alert("Problemas con el audio \n'.$array[$i][6].'");
                    window.location.href="words/lote";
                    </script>';
            }
            if($array[$i][4]!=''){
                if(!File::exists('storage/dclo_public/img_out_L/'.$carpeta_imagen.'/'.$array[$i][4].'.svg')){
                    File::deleteDirectory(public_path('storage/dclo_public/audios/'.$carpeta_audio));
                    File::deleteDirectory(public_path('storage/dclo_public/img_out_L/'.$carpeta_imagen));
                    File::deleteDirectory(public_path('storage/dclo_public/audios/'.$nueva_carpeta));
                    File::deleteDirectory(public_path('storage/dclo_public/img_out_L/'.$nueva_carpeta));
                    $this->eliminar($fecha);
                    echo'<script type="text/javascript">
                    alert("Problemas con la imagen \n'.$array[$i][4].'");
                    window.location.href="words/lote";
                    </script>';
                }
            }
            
            if($this->getDuplicado($array[0][2],$array[$i][2])==null){
                                /******************************** generic judgment *******************************/
                // dd($GJ);
                
                $GJ = $this->getGenericJudgments($array[$i][3], $nWords, $tam_generic, $i, $fecha);
                $generic_judgments = new generic_judgments();
                $generic_judgments->id_generic_judgment = $GJ[0][0];
                $generic_judgments->cat_data_type = $GJ[0][1];
                $generic_judgments->cat_state = $GJ[0][2];
                $generic_judgments->obs = $GJ[0][3];
                $generic_judgments->created_by= $GJ[0][4];
                $generic_judgments->created_at = $GJ[0][5];
                $generic_judgments->updated_by = $GJ[0][6];
                $generic_judgments->updated_at = $GJ[0][7];
                $generic_judgments->deleted_by = $GJ[0][8];
                $generic_judgments->deleted_at = $GJ[0][9];
                $generic_judgments->save();
                /* esp */
                $generic_judgments = new generic_judgments();
                $generic_judgments->id_generic_judgment = $GJ[1][0];
                $generic_judgments->cat_data_type = $GJ[1][1];
                $generic_judgments->cat_state = $GJ[1][2];
                $generic_judgments->obs = $GJ[1][3];
                $generic_judgments->created_by= $GJ[1][4];
                $generic_judgments->created_at = $GJ[1][5];
                $generic_judgments->updated_by = $GJ[1][6];
                $generic_judgments->updated_at = $GJ[1][7];
                $generic_judgments->deleted_by = $GJ[1][8];
                $generic_judgments->deleted_at = $GJ[1][9];
                $generic_judgments->save();
                /* def_mining */
                $generic_judgments = new generic_judgments();
                $generic_judgments->id_generic_judgment = $GJ[2][0];
                $generic_judgments->cat_data_type = $GJ[2][1];
                $generic_judgments->cat_state = $GJ[2][2];
                $generic_judgments->obs = $GJ[2][3];
                $generic_judgments->created_by= $GJ[2][4];
                $generic_judgments->created_at = $GJ[2][5];
                $generic_judgments->updated_by = $GJ[2][6];
                $generic_judgments->updated_at = $GJ[2][7];
                $generic_judgments->deleted_by = $GJ[2][8];
                $generic_judgments->deleted_at = $GJ[2][9];
                $generic_judgments->save();
                /********************************** words ***************************************/
                /* Consulta_WS */
                $WS = $this->getWords($array[$i][2],$array[$i][3],$nWords,$tam_words,$tam_generic,$i,$id_language,$fecha);
                $words = new words();
                $words->id_word = $WS[0][0];
                $words->id_language_from = $WS[0][1];
                $words->id_generic_judgment = $WS[0][2];
                $words->grapheme = $WS[0][3];
                $words->cat_dialectal_variation = $WS[0][4];
                $words->cat_word_origin = $WS[0][5];
                $words->cat_word_status = $WS[0][6];
                $words->id_external = $WS[0][7];
                $words->cat_status = $WS[0][8];
                $words->cat_word_status= $WS[0][9];
                $words->created_by= $WS[0][10];
                $words->created_at = $WS[0][11];
                $words->updated_by = $WS[0][12];
                $words->updated_at = $WS[0][13];
                $words->deleted_by = $WS[0][14];
                $words->deleted_at = $WS[0][15];
                $words->save();
                /*  Consulta_WS_esp */
                $words = new words();
                $words->id_word = $WS[1][0];
                $words->id_language_from = $WS[1][1];
                $words->id_generic_judgment = $WS[1][2];
                $words->grapheme = $WS[1][3];
                $words->cat_dialectal_variation = $WS[1][4];
                $words->cat_word_origin = $WS[1][5];
                $words->cat_word_status = $WS[1][6];
                $words->id_external = $WS[1][7];
                $words->cat_status = $WS[1][8];
                $words->cat_word_status= $WS[1][9];
                $words->created_by= $WS[1][10];
                $words->created_at = $WS[1][11];
                $words->updated_by = $WS[1][12];
                $words->updated_at = $WS[1][13];
                $words->deleted_by = $WS[1][14];
                $words->deleted_at = $WS[1][15];
                $words->save();
                /*  WORD_DEFINITIONS Consulta_WD  */
                $WD = $this->getWordDefinitions($array[$i][5] ,$tam_word_definitions, $i, $fecha);
                $word_definitions = new word_definitions();
                $word_definitions->id_word_definition = $WD[0];
                $word_definitions->key_words = $WD[1];
                $word_definitions->description = $WD[2];
                $word_definitions->get_media_priority = $WD[3];
                $word_definitions->created_by = $WD[4];
                $word_definitions->created_at = $WD[5];
                $word_definitions->updated_by = $WD[6];
                $word_definitions->updated_at = $WD[7];
                $word_definitions->deleted_by = $WD[8];
                $word_definitions->deleted_at = $WD[9];
                $word_definitions->save();
                /********************************** definition categories ***************************************/
                /* Consulta_DC */
                $DC = $this->getDefinitionCategories($array[$i][1] ,$tam_definition_categories ,$tam_word_definitions ,$i, $fecha);
                $definition_categories = new definition_categories();
                $definition_categories->id_definition_category = $DC[0];
                $definition_categories->id_word_definition = $DC[1];
                $definition_categories->cat_category = $DC[2];
                $definition_categories->created_by = $DC[3];
                $definition_categories->created_at = $DC[4];
                $definition_categories->updated_by = $DC[5];
                $definition_categories->updated_at = $DC[6];
                $definition_categories->deleted_by = $DC[7];
                $definition_categories->deleted_at = $DC[8];
                $definition_categories->save();
                /********************************** definition meaning ***************************************/
                /* Consulta_DM */
                $DM = $this->getDefinitionMeanings($array[$i][5] ,$tam_definition_meanings ,$tam_word_definitions, $tam_generic, $nWords ,$i, $fecha);
                $definition_meanings = new definition_meanings();
                $definition_meanings->id_definition_meaning = $DM[0];
                $definition_meanings->id_language_to = $DM[1];
                $definition_meanings->id_word_definition = $DM[2];
                $definition_meanings->id_generic_judgment = $DM[3];
                $definition_meanings->meaning = $DM[4];
                $definition_meanings->cat_meaning_type = $DM[5];
                $definition_meanings->cat_meaning_origin = $DM[6];
                $definition_meanings->default_meaning = $DM[7];
                $definition_meanings->relevance = $DM[8];
                $definition_meanings->id_external = $DM[9];
                $definition_meanings->cat_status = $DM[10];
                $definition_meanings->created_by = $DM[11];
                $definition_meanings->created_at = $DM[12];
                $definition_meanings->updated_by = $DM[13];
                $definition_meanings->updated_at = $DM[14];
                $definition_meanings->deleted_by = $DM[15];
                $definition_meanings->deleted_at = $DM[16];
                $definition_meanings->save();
                /********************************** archivos files Consulta_FL***************************************/
                //public function getArchivosFilesAudio($tam_files, $tam_words, $nWords, $i, $fecha, $carpeta_audio, $nueva_carpeta, $nro_audio){

                $AF = $this->getArchivosFilesAudio($tam_files, $tam_words, $nWords, $i, $fecha, $carpeta_audio, $nueva_carpeta, $array[$i][6]);
                $files = new files();
                $files->id_file = $AF[0];
                $files->description = $AF[1];
                $files->mime_type = $AF[2];
                $files->cat_file_type = $AF[3];
                $files->cat_storage_type = $AF[4];
                $files->path = $AF[5];
                $files->cat_licence_type = $AF[6];
                $files->author_name = $AF[7];
                $files->url_origin = $AF[8];
                $files->created_by = $AF[9];
                $files->created_at = $AF[10];
                $files->updated_by = $AF[11];
                $files->updated_at = $AF[12];
                $files->deleted_by = $AF[13];
                $files->deleted_at = $AF[14];
                $files->save();
                $id_WS_= $tam_words+$i;
                //$id_WS = $tam_words+$i;
                //$id_WS = $tam_words+$i;
                if(strlen($array[$i][4])>0){                 
                    ++$id_GJ_webmedia;
                    ++$id_DMed;
                    ++$id_FL_img;

                    $GJ_W = $this->getGenericJudgments_W($id_GJ_webmedia,$fecha);
                    $generic_judgments = new generic_judgments();
                    $generic_judgments->id_generic_judgment = $GJ_W[0];
                    $generic_judgments->cat_data_type = $GJ_W[1];
                    $generic_judgments->cat_state = $GJ_W[2];
                    $generic_judgments->obs = $GJ_W[3];
                    $generic_judgments->created_by = $GJ_W[4];
                    $generic_judgments->created_at = $GJ_W[5];
                    $generic_judgments->updated_by = $GJ_W[6];
                    $generic_judgments->updated_at = $GJ_W[7];
                    $generic_judgments->deleted_by = $GJ_W[8];
                    $generic_judgments->deleted_at = $GJ_W[9];
                    $generic_judgments->save();
                  // public function getArchivosFilesImagen($id_FL_img,$tam_words, $i, $fecha,$carpeta_imagen, $nueva_carpeta, $nom_imagen){

                    $IF = $this->getArchivosFilesImagen($id_FL_img,$tam_words, $i, $fecha, $carpeta_imagen, $nueva_carpeta, $array[$i][4]);
                    $files = new files();
                    $files->id_file = $IF[0];
                    $files->description = $IF[1];
                    $files->mime_type = $IF[2];
                    $files->cat_file_type = $IF[3];
                    $files->cat_storage_type = $IF[4];
                    $files->path = $IF[5];
                    $files->cat_licence_type = $IF[6];
                    $files->author_name = $IF[7];
                    $files->url_origin = $IF[8];
                    $files->created_by = $IF[9];
                    $files->created_at = $IF[10];
                    $files->updated_by = $IF[11];
                    $files->updated_at = $IF[12];
                    $files->deleted_by = $IF[13];
                    $files->deleted_at = $IF[14];
                    $files->save();

                    $DMed = $this->getDefinitionMedias($id_DMed,$id_GJ_webmedia,$id_FL_img,$tam_word_definitions, $fecha, $i);
                    $definition_medias = new definition_medias();
                    $definition_medias->id_definition_media = $DMed[0];
                    $definition_medias->id_file = $DMed[1];
                    $definition_medias->id_word_definition = $DMed[2];
                    $definition_medias->id_generic_judgment = $DMed[3];
                    $definition_medias->cat_def_media_type = $DMed[4];
                    $definition_medias->cat_media_origin = $DMed[5];
                    $definition_medias->default_resource = $DMed[6];
                    $definition_medias->relevance = $DMed[7];
                    $definition_medias->id_external = $DMed[8];
                    $definition_medias->cat_status = $DMed[9];
                    $definition_medias->created_by = $DMed[10];
                    $definition_medias->created_at = $DMed[11];
                    $definition_medias->updated_by = $DMed[12];
                    $definition_medias->updated_at = $DMed[13];
                    $definition_medias->deleted_by = $DMed[14];
                    $definition_medias->deleted_at = $DMed[15];
                    $definition_medias->save();
                }
                /**********************************`audio_judgments`*Consulta_AJ**************************************/
                $AJ = $this->getAudioJ($array[$i][3],$tam_audio_judgments,$i,$fecha);
                $audio_judgments = new audio_judgments();
                $audio_judgments->id_audio_judgment = $AJ[0];
                $audio_judgments->cat_state = $AJ[1];
                $audio_judgments->obs = $AJ[2];
                $audio_judgments->cat_speaker_data = $AJ[3];
                $audio_judgments->created_by = $AJ[4];
                $audio_judgments->created_at = $AJ[5];
                $audio_judgments->updated_by = $AJ[6];
                $audio_judgments->updated_at = $AJ[7];
                $audio_judgments->deleted_by = $AJ[8];
                $audio_judgments->deleted_at = $AJ[9];
                $audio_judgments->save();
                /********************************`word_audios`**Consulta_WA**************************************/
                $WA = $this->getWordAudios($tam_word_audios,$tam_words,$tam_files,$tam_audio_judgments,$i,$fecha);
                $word_audios = new word_audios();
                $word_audios->id_word_audio = $WA[0];
                $word_audios->id_word = $WA[1];
                $word_audios->id_file = $WA[2];
                $word_audios->id_audio_judgment = $WA[3];
                $word_audios->cat_audio_origin = $WA[4];
                $word_audios->default_resource = $WA[5];
                $word_audios->quality_level = $WA[6];
                $word_audios->relevance = $WA[7];
                $word_audios->id_external = $WA[8];
                $word_audios->cat_status = $WA[9];
                $word_audios->created_by = $WA[10];
                $word_audios->created_at = $WA[11];
                $word_audios->updated_by = $WA[12];
                $word_audios->updated_at = $WA[13];
                $word_audios->deleted_by = $WA[14];
                $word_audios->deleted_at = $WA[15];
                $word_audios->save();
    /********************************``word2definitions``**Consulta_W2D**************************************/
                $W2D = $this->getWord2Definitions($tam_word2definitions,$tam_words,$tam_word_definitions,$nWords,$i,$fecha);
                $word2definitions = new word2definitions();
                $word2definitions->id_word2definition = $W2D[0][0];
                $word2definitions->id_word = $W2D[0][1];
                $word2definitions->id_word_definition = $W2D[0][2];
                $word2definitions->comments = $W2D[0][3];
                $word2definitions->cat_word_status = $W2D[0][4];
                $word2definitions->created_by = $W2D[0][5];
                $word2definitions->created_at = $W2D[0][6];
                $word2definitions->updated_by = $W2D[0][7];
                $word2definitions->updated_at = $W2D[0][8];
                $word2definitions->deleted_by = $W2D[0][9];
                $word2definitions->deleted_at = $W2D[0][10];
                $word2definitions->save();
            /*Consulta_W2D_esp*/
                $word2definitions = new word2definitions();
                $word2definitions->id_word2definition = $W2D[1][0];
                $word2definitions->id_word = $W2D[1][1];
                $word2definitions->id_word_definition = $W2D[1][2];
                $word2definitions->comments = $W2D[1][3];
                $word2definitions->cat_word_status = $W2D[1][4];
                $word2definitions->created_by = $W2D[1][5];
                $word2definitions->created_at = $W2D[1][6];
                $word2definitions->updated_by = $W2D[1][7];
                $word2definitions->updated_at = $W2D[1][8];
                $word2definitions->deleted_by = $W2D[1][9];
                $word2definitions->deleted_at = $W2D[1][10];
                $word2definitions->save();

            }else{
                $elim[$con] = $this->getDuplicado($array[0][2],$array[$i][2]);
                $con++;
                $eliminados = $eliminados.' \n'.$this->getDuplicado($array[0][2],$array[$i][2]);
                //Storage::disk('public')->delete('/audios/'.$carpeta_audio.'/'.$array[$i][6].'.mp3');

                //eliminar el audio
            }
            $con2++;

        }
        File::deleteDirectory(public_path('storage/dclo_public/audios/'.$carpeta_audio));
        File::deleteDirectory(public_path('storage/dclo_public/img_out_L/'.$carpeta_imagen));
        if($con==$con2){
            File::deleteDirectory(public_path('storage/dclo_public/audios/'.$nueva_carpeta));
            File::deleteDirectory(public_path('storage/dclo_public/img_out_L/'.$nueva_carpeta));
        }
        $id_ver = $tam_words+1;
        if($con!=0){
            $eliminados = $con.' DUPLICADOS \n'.$eliminados;
            echo'<script type="text/javascript">
            alert("'.$eliminados.'");
            window.location.href="words/vistalote'.$id_ver.'/'.$id_language.'";
            </script>'; 
        }else
            return redirect('words/vistalote'.$id_ver.'/'.$id_language);
                        //$this->vistalote_fecha($id_ver);



       
    }
    public function vistalote_fecha($id,$id_language_from){
       
        $fecha = $this->getFechaLote($id);

        if(!$fecha)
            $fecha = Carbon::parse(date("Y-m-d h:m:s"));

        //dd($fecha);

        $idioma = $this->getIdiomaLote($id);
        $nueva_carpeta = $this->getCarpetaLote($id);
        $word = App\words::distinct()
        ->select(
        'words.id_word',
        'words.grapheme',
        'words.id_language_from',
        'words.created_at',
        'generic_judgments.obs',
        'generic_judgments.cat_data_type',
        'word_definitions.key_words',
        'definition_categories.cat_category',
        'definition_meanings.meaning',
        'files.path',
        'word2definitions.id_word_definition',
        'language_name'
        )
            ->leftjoin('languages','words.id_language_from','=','languages.id_language')
            ->leftjoin('word2definitions','words.id_word','=','word2definitions.id_word')
            ->leftjoin('word_definitions','word2definitions.id_word_definition','=','word_definitions.id_word_definition')
            ->leftjoin('definition_categories','word_definitions.id_word_definition','=','definition_categories.id_word_definition')
            ->leftjoin('definition_meanings','word_definitions.id_word_definition','=','definition_meanings.id_word_definition')
            ->leftjoin('generic_judgments','definition_meanings.id_generic_judgment','=','generic_judgments.id_generic_judgment')
         // ->where('words.id_generic_judgment','=','generic_judgments.id_generic_judgment')
            /*->leftjoin('word_audios','words.id_word','=','word_audios.id_word')
            ->leftjoin('files','word_audios.id_file','=','files.id_file')*/
            ->leftjoin('definition_medias','word_definitions.id_word_definition','=','definition_medias.id_word_definition')
            ->leftjoin('files','definition_medias.id_file','=','files.id_file')
            ->where('words.created_at','=',$fecha)
            ->where('words.id_language_from','!=',1)
            ->where('words.id_language_from','=',$id_language_from)

            ->orderBy('id_word')
            ->paginate(15);
            //dd($fecha);
            return view('import.vistalote',compact('word'))->with('fecha',$fecha)->with('idioma',$idioma)->with('nueva_carpeta',$nueva_carpeta)->with('id_language_from',$id_language_from);
            //return view('vistalote'.$id,compact('word'))->with('fecha',$fecha)->with('idioma',$idioma)->with('nueva_carpeta',$nueva_carpeta);
            //return 'h';
    }
    public function listarlote(){
        $lotes = App\words::distinct()
        ->select(
        'words.created_at','words.id_language_from','languages.language_name'
        )
        ->join('languages','languages.id_language','=','words.id_language_from')
        ->distinct('words.created_at','words.id_language_from')
            ->orderBy('created_at', 'desc')
            //
            ->where('id_word','!=',0)
            ->where('id_language_from','!=',1)
            //->paginate(500);
            ->get();
            return view('import.listarlote',compact('lotes'));      
    }

    public static function audio($id_word){
        $word = App\words::distinct()
            ->select('files.path')
            ->where('words.id_word','=',$id_word)
            ->join('word_audios','words.id_word','=','word_audios.id_word')
            ->join('files','word_audios.id_file','=','files.id_file')

            //->with('grapheme',$grapheme)
            ->get()
            ->first();
            //->getQuery();
            //compact('word');
           $cad = $word['path'];
        return $cad;        
    }
    public function getWord2Definitions($tam_word2definitions,$tam_words,$tam_word_definitions,$nWords,$i,$fecha){
        
        $m=array();
        $m[0][0] = (int)$tam_word2definitions+$i;
        $m[0][1] = $tam_words+$i;
        $m[0][2] = $tam_word_definitions+$i;
        $m[0][3] = 'OBS-COMENTARIOS';
        $m[0][4] = 'VGNT';
        $m[0][5] = 0;
        $m[0][6] = $fecha;
        $m[0][7] = 0;
        $m[0][8] = $fecha;
        $m[0][9] = null;
        $m[0][10] = null;
        /*Consulta_W2D_esp*/
        $m[1][0] = (int)$tam_word2definitions+$nWords+$i;
        $m[1][1] = $tam_words+$nWords+$i;
        $m[1][2] = $tam_word_definitions+$i;
        $m[1][3] = 'OBS-COMENTARIOS';
        $m[1][4] = 'VGNT';
        $m[1][5] = 0;
        $m[1][6] = $fecha;
        $m[1][7] = 0;
        $m[1][8] = $fecha;
        $m[1][9] = null;
        $m[1][10] = null;   
        
        return $m;
    }
    public function getWordAudios($tam_word_audios,$tam_words,$tam_files,$tam_audio_judgments,$i,$fecha){
        $m=array();
        $m[0] = (int)$tam_word_audios+$i;
        $m[1] = $tam_words+$i;
        $m[2] = $tam_files+$i;
        $m[3] = $tam_audio_judgments+$i;
        $m[4] = 'OFL';
        $m[5] = 1;
        $m[6] = 70;
        $m[7] = 10;
        $m[8] = null;
        $m[9] = 'OFL';
        $m[10] = 0;
        $m[11] = $fecha;
        $m[12] = 0;
        $m[13] = $fecha;
        $m[14] = null;
        $m[15] = null;

        return $m;
    }
    public function getAudioJ($valor,$tam_audio_judgments,$i,$fecha){
        $m=array();
        if($this->getFrase($valor)){
            $m[0] = $tam_audio_judgments+$i;
            $m[1] = 'ACCPT';
            $m[2] = 'FRASE-PEDAGÓGICO';
            $m[3] = 'ACCPT';
            $m[4] = 0;
            $m[5] = $fecha;
            $m[6] = 0;
            $m[7] = $fecha;
            $m[8] = null;
            $m[9] = null;
        }else{
            $m[0] = (int)$tam_audio_judgments+$i;
            $m[1] = 'ACCPT';
            $m[2] = 'VOCABULARIO-PEDAGÓGICO';
            $m[3] = 'ACCPT';
            $m[4] = 0;
            $m[5] = $fecha;
            $m[6] = 0;
            $m[7] = $fecha;
            $m[8] = null;
            $m[9] = null;
        }
        //dd($m);
        return $m;
    }
    public function getDefinitionMedias($id_DMed,$id_GJ_webmedia,$id_FL_img,$tam_word_definitions, $fecha, $i){
        /* Consulta_DMed */
        $m=array();
        $m[0] = (int)$id_DMed;//id_file_imagen
        $m[1] = (int)$id_FL_img;
        $m[2] = (int)$tam_word_definitions+$i;
        $m[3] = (int)$id_GJ_webmedia;
        $m[4] = 'IMG';
        $m[5] = 'OFL';
        $m[6] = 1;
        $m[7] = 70;
        $m[8] = null;
        $m[9] = 'OFL';
        $m[10] = 0;
        $m[11] = $fecha;
        $m[12] = 0;
        $m[13] = $fecha;
        $m[14] = null;
        $m[15] = null;

        return $m;
    }

    public function getArchivosFilesImagen($id_FL_img,$tam_words, $i, $fecha,$carpeta_imagen, $nueva_carpeta, $nom_imagen){
        /* Consulta_file imagen */
        $m=array();
        $id_WS = $tam_words+$i;
        $m[0] = (int)$id_FL_img;//id_file_imagen
        $m[1] = 'descripcion imagen - ' . $id_WS . '.svg';
        $m[2] = 'image/svg';
        $m[3] = 'IMAGE';
        $m[4] = 'vps';
        $m[5] = $nueva_carpeta."/".strval($id_WS).".svg";//'"+ l_locale + "/" + contTema + "/" + id_WS+ ".svg',
        $m[6] = 'NONE';
        $m[7] = 'author-name';
        $m[8] = 'https://goo.gl/u5H65S';
        $m[9] = 0;
        $m[10] = $fecha;
        $m[11] = 0;
        $m[12] = $fecha;
        $m[13] = null;
        $m[14] = null;
        exec('mv storage/dclo_public/img_out_L/'.$carpeta_imagen.'/'.$nom_imagen.'.svg storage/dclo_public/img_out_L/'.$m[5]);

        return $m;
 
    }
    public function getGenericJudgments_W($id_GJ_webmedia,$fecha){
        $m=array();
    /* Consulta_GJ_webmedia */
        $m[0] = (int)$id_GJ_webmedia;
        $m[1] = 'WMEDIA';
        $m[2] = 'ACCPT';
        $m[3] = 'VOCABULARIO-PEDAGÓGICO';
        $m[4] = 0;
        $m[5] = $fecha;
        $m[6] = 0;
        $m[7] = $fecha;
        $m[8] = null;
        $m[9] = null;

        return $m;
    }
    public function getArchivosFilesAudio($tam_files, $tam_words, $nWords, $i, $fecha, $carpeta_audio, $nueva_carpeta, $nro_audio){
        $m=array();
        /* Consulta_FL */
        //date("d-m-Y")        $fecha = date("Y-m-d h:m:s");
        $id_WS = $tam_words+$i;
        $m[0] = (int)$tam_files+$i;//id_file
        $m[1] = 'descripcion audio - ' .strval($id_WS) . '.mp3';
        $m[2] = 'audio/mp3';
        $m[3] = 'AUDIO';
        $m[4] = 'vps';
        $m[5] = $nueva_carpeta."/".strval($id_WS).".mp3";
        $m[6] = 'NONE';
        $m[7] = 'author-name';
        $m[8] = 'https://goo.gl/u5H65S';
        $m[9] = 0;
        $m[10] = $fecha;
        $m[11] = 0;
        $m[12] = $fecha;
        $m[13] = null;
        $m[14] = null;
        //$ruta = '/opt/lampp/htdocs/oei/storage/app/public/archivos/';
        //$ruta = '/opt/lampp/htdocs/oei/public/assets/archivos/';
        

        exec('mv storage/dclo_public/audios/'.$carpeta_audio.'/'.$nro_audio.'.mp3 storage/dclo_public/audios/'.$m[5]);

        return $m;
 
    }

    public function getDefinitionMeanings($definicion ,$tam_definition_meanings ,$tam_word_definitions, $tam_generic, $nWords ,$i, $fecha){
        $m=array();
        $m[0] = (int)$tam_definition_meanings+$i;//id_DM
        $m[1] = 1;
        $m[2] = $tam_word_definitions+$i;//id_WD
        $m[3] = $tam_generic+$nWords+$i;//id_GJ_esp
        $m[4] = $definicion;
        $m[5] = 'FRASE';
        $m[6] = 'OFL';
        $m[7] = 1;
        $m[8] = 10;
        $m[9] = null;
        $m[10] = 'OFL';
        $m[11] = 0;
        $m[12] = $fecha;
        $m[13] = 0;
        $m[14] = $fecha;
        $m[15] = null;
        $m[16] = null;
        //dd($m);
        return $m;
    }
    public function getDefinitionCategories($valor ,$tam_definition_categories ,$tam_word_definitions ,$i, $fecha){
        /* if ($valor==("1")) {
            contTema++;
            categoria = "Escuela-" + contTema;
            OrdenImagenes += "mkdir -p " + l_locale + "_i/" + contTema + "\n";
            OrdenAudios += "mkdir -p " + l_locale + "_a/" + contTema + "\n";
            }
        */
        $m=array();
        $m[0] = (int)$tam_definition_categories+$i;//id_DC
        $m[1] = (int)$tam_word_definitions+$i;//id_WD
        $m[2] = strtoupper('Escuela');
        $m[3] = 0;
        $m[4] = $fecha;
        $m[5] = 0;
        $m[6] = $fecha;
        $m[7] = null;
        $m[8] = null;

        return $m;
    }
    public function getWordDefinitions($key_words ,$tam_word_definitions, $i, $fecha){
        $m=array();
        $m[0] = (int) $tam_word_definitions+$i;
        $m[1] = $key_words;
        $m[2] = 'descripcion';
        $m[3] = 0;
        $m[4] = 0;
        $m[5] = $fecha;
        $m[6] = 0;
        $m[7] = $fecha;
        $m[8] = null;
        $m[9] = null;
        //dd($m);
        return $m;
    }
    public function getWords($grapheme, $grapheme_esp, $nWords, $tam_words, $tam_generic, $i, $id_language, $fecha){
        $m=array();
        /* Consulta_WS */
        $m[0][0] = (int)$tam_words+$i;//id_WS
        $m[0][1] = $id_language ;//id_language 
        $m[0][2] = (int) $tam_generic+$i;//id_GJ
        $m[0][3] = $grapheme;
        $m[0][4] = 'ALTIPLANO';
        $m[0][5] = 'OFL';
        $m[0][6] = 'VGNT';
        $m[0][7] = 0;
        $m[0][8] = null;
        $m[0][9] = 'OFL';
        $m[0][10] = 0;
        $m[0][11] = $fecha;
        $m[0][12] = 0;
        $m[0][13] = $fecha;
        $m[0][14] = null;
        $m[0][15] = null;
       /*  Consulta_WS_esp */
        $m[1][0] = (int)$tam_words+$nWords+$i;//id_WS_esp
        $m[1][1] = 1;//id_language_esp
        $m[1][2] = (int) $tam_generic+$nWords+$i;//id_GJ_esp
        $m[1][3] = $grapheme_esp;
        $m[1][4] = 'ALTIPLANO';
        $m[1][5] = 'OFL';
        $m[1][6] = 'VGNT';
        $m[1][7] = 0;
        $m[1][8] = null;
        $m[1][9] = 'OFL';
        $m[1][10] = 0;
        $m[1][11] = $fecha;
        $m[1][12] = 0;
        $m[1][13] = $fecha;
        $m[1][14] = null;
        $m[1][15] = null;
        //dd($m);

        return $m;
    }
    public function getGenericJudgments($valor, $nWords, $tam_generic,$i,$fecha){
        $m=array();
        if($this->getFrase($valor)){
            /* Consulta_GJ */
            $id_gd = (int)$tam_generic+$i;
            $m[0][0] = $id_gd;
            $m[0][1] = 'SNTNC';
            $m[0][2] = 'ACCPT';
            $m[0][3] = 'FRASE-PEDAGÓGICO';
            $m[0][4] = '0';
            $m[0][5] = $fecha;
            $m[0][6] = '0';
            $m[0][7] = $fecha;
            $m[0][8] = null;
            $m[0][9] = null;
            /* esp */
            $id_GJ_esp = (int)$tam_generic+$nWords+$i;
            $m[1][0] = $id_GJ_esp;
            $m[1][1] = 'SNTNC';
            $m[1][2] = 'ACCPT';
            $m[1][3] = 'FRASE-PEDAGÓGICO';
            $m[1][4] = '0';
            $m[1][5] = $fecha;
            $m[1][6] = '0';
            $m[1][7] = $fecha;
            $m[1][8] = null;
            $m[1][9] = null;
            /* def_mining */
            $id_def_min = $tam_generic+($nWords*2)+$i;
            $m[2][0] = (int)$id_def_min; 
            $m[2][1] = 'DEF_MNING';
            $m[2][2] = 'ACCPT';
            $m[2][3] = 'FRASE-PEDAGÓGICO';
            $m[2][4] = '0';
            $m[2][5] = $fecha;
            $m[2][6] = '0';
            $m[2][7] = $fecha;
            $m[2][8] = null;
            $m[2][9] = null;

        }
        else{         
            /* Consulta_GJ */
            $id_gd = (int)$tam_generic+$i;
            $m[0][0] = (string)$id_gd;
            $m[0][1] = 'WORD';
            $m[0][2] = 'ACCPT';
            $m[0][3] = 'VOCABULARIO-PEDAGÓGICO';
            $m[0][4] = '0';
            $m[0][5] = $fecha;
            $m[0][6] = '0';
            $m[0][7] = $fecha;
            $m[0][8] = null;
            $m[0][9] = null;
            /* esp */
            $id_GJ_esp = (int)$tam_generic+$nWords+$i;
            $m[1][0] = (string) $id_GJ_esp;
            $m[1][1] = 'WORD';
            $m[1][2] = 'ACCPT';
            $m[1][3] = 'VOCABULARIO-PEDAGÓGICO';
            $m[1][4] = '0';
            $m[1][5] = $fecha;
            $m[1][6] = '0';
            $m[1][7] = $fecha;
            $m[1][8] = null;
            $m[1][9] = null;
            /* def_mining */
            $id_def_min = $tam_generic+($nWords*2)+$i;
            $m[2][0] = (int)$id_def_min; 
            $m[2][1] = 'DEF_MNING';
            $m[2][2] = 'ACCPT';
            $m[2][3] = 'VOCABULARIO-PEDAGÓGICO';
            $m[2][4] = '0';
            $m[2][5] = $fecha;
            $m[2][6] = '0';
            $m[2][7] = $fecha;
            $m[2][8] = null;
            $m[2][9] = null;
        }
      //  dd($m);
        return $m;
    }
    public function getFrase($cad){
        $words = preg_split("/[\s,]+/",$cad);
        //dd($words);
        //dd(count($words));
		if (count($words) > 1)
			return true;
		return false;
    }
    public function getIdTamWords(){
        $word = App\words::distinct()
        ->orderby('id_word','DESC')->take(1)->get()->first();
        $id = $word['id_word'];
        return ($id);
    }
    public function getIdGenericJudgments(){
        $word = App\generic_judgments::distinct()
        ->orderby('id_generic_judgment','DESC')->take(1)->get()->first();
        $id = $word['id_generic_judgment'];
        return ($id);
    }
    public function getIdWordDefinitions(){
        $word = App\word_definitions::distinct()
        ->orderby('id_word_definition','DESC')->take(1)->get()->first();
        $id = $word['id_word_definition'];
        return ($id);
    }
    public function getIdDefinitionCategories(){
        $word = App\definition_categories::distinct()
        ->orderby('id_definition_category','DESC')->take(1)->get()->first();
        $id = $word['id_definition_category'];
        return ($id);
    }
    
    public function getIdDefinitionMeanings(){
        $word = App\definition_meanings::distinct()
        ->orderby('id_definition_meaning','DESC')->take(1)->get()->first();
        $id = $word['id_definition_meaning'];
        return ($id);
    }
    public function getIdFiles(){
        $word = App\files::distinct()
        ->orderby('id_file','DESC')->take(1)->get()->first();
        $id = $word['id_file'];
        return ($id);
    }    
    public function getIdDefinitionMedias(){
        $word = definition_medias::distinct()
        ->orderby('id_definition_media','DESC')->take(1)->get()->first();
        $id = $word['id_definition_media'];
        return ($id);
    }
    public function getIdAudioJ(){
        $word = audio_judgments::distinct()
        ->orderby('id_audio_judgment','DESC')->take(1)->get()->first();
        $id = $word['id_audio_judgment'];
        return ($id);
    }
    public function getIdWordAudios(){
        $word = word_audios::distinct()
        ->orderby('id_word_audio','DESC')->take(1)->get()->first();
        $id = $word['id_word_audio'];
        return ($id);
    }
    public function getIdWord2Definitions(){
        $word = word2definitions::distinct()
        ->orderby('id_word2definition','DESC')->take(1)->get()->first();
        $id = $word['id_word2definition'];
        return ($id);
    }
    public function getDuplicado($language_name,$grapheme){
        $id_language=$this->getId_Language($language_name);
        $word = App\words::distinct()
            ->select('words.grapheme')
            ->where('words.grapheme','=',$grapheme)
           ->where('words.id_language_from','=',$id_language)
            ->get()
            ->first();
           $cad = $word['grapheme'];
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
    public function getFechaLote($id){
        $word = App\words::distinct()
            ->select('words.created_at')
            ->where('words.id_word','=',$id)
            ->get()
            ->first();
            $fecha = $word['created_at'];
        return $fecha;
    }
    public static function getIdLote($fecha,$id_language_from){
        $word = App\words::distinct()
            ->select('words.id_word')
            ->where('words.created_at','=',$fecha)
            ->where('words.id_language_from','=',$id_language_from)
            ->get()
            ->first();
            $id = $word['id_word'];
        return $id;
    } 
    public function getIdiomaLote($id){
        $word = App\words::distinct()
            ->select('languages.language_name')
            ->where('words.id_word','=',$id)
            ->join('languages','words.id_language_from','=','languages.id_language')
            ->get()
            ->first();
            $idioma = $word['language_name'];
        return $idioma;
    } 
    public function getCarpetaLote($id){
        $item = App\words::distinct()
        ->select('files.path')
        ->join('word_audios','words.id_word','=','word_audios.id_word')
        ->join('files','word_audios.id_file','=','files.id_file')
        ->where('words.id_word','=',$id)
        ->get()
        ->first();
        $ruta_audio = $item['path'];
        $cadena = (explode("/",$ruta_audio));
        $ruta='';
        for($i=0;$i<sizeof($cadena)-1;++$i)
            $ruta = $ruta.$cadena[$i].'/';
        return $ruta;
    } 
    public static function contarPalabra($fecha,$id_language_from){
        $item = App\words::distinct()
        ->select('words.grapheme')
        ->where('words.created_at','=',$fecha)
        ->where('words.id_language_from','=',$id_language_from)
        ->where('words.id_language_from','!=','1')
        ->count();
       // dd($item);
        return $item;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function lote()
    {
        return view('/import.lote');
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
        //
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
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $nueva_carpeta)
    {
        //dd($nueva_carpeta);
        //$item = App\word2definitions::find($id);
        if($id!=null && $nueva_carpeta!=null){
            $item = App\word2definitions::distinct()
            ->where('word2definitions.created_at','=',$id)
            ->delete();
            $item = App\word_audios::distinct()
            ->where('word_audios.created_at','=',$id)
            ->delete();
            $item = App\audio_judgments::distinct()
            ->where('audio_judgments.created_at','=',$id)
            ->delete();
            $item = App\definition_medias::distinct()
            ->where('definition_medias.created_at','=',$id)
            ->delete();
            $item = App\files::distinct()
            ->where('files.created_at','=',$id)
            ->delete();
            $item = App\definition_meanings::distinct()
            ->where('definition_meanings.created_at','=',$id)
            ->delete();
            $item = App\definition_categories::distinct()
            ->where('definition_categories.created_at','=',$id)
            ->delete();
            $item = App\word_definitions::distinct()
            ->where('word_definitions.created_at','=',$id)
            ->delete();
            $item = App\words::distinct()
            ->where('words.created_at','=',$id)
            ->delete();
            $item = App\generic_judgments::distinct()
            ->where('generic_judgments.created_at','=',$id)
            ->delete();
            File::deleteDirectory(public_path('storage/dclo_public/audios/'.$nueva_carpeta));
            File::deleteDirectory(public_path('storage/dclo_public/img_out_L/'.$nueva_carpeta));
        }
	    return redirect('words/lote');
    }
    public function eliminar($id)
    {
        $item = App\word2definitions::distinct()
        ->where('word2definitions.created_at','=',$id)
        ->delete();
        $item = App\word_audios::distinct()
        ->where('word_audios.created_at','=',$id)
        ->delete();
        $item = App\audio_judgments::distinct()
        ->where('audio_judgments.created_at','=',$id)
        ->delete();
        $item = App\definition_medias::distinct()
        ->where('definition_medias.created_at','=',$id)
        ->delete();
        $item = App\files::distinct()
        ->where('files.created_at','=',$id)
        ->delete();
        $item = App\definition_meanings::distinct()
        ->where('definition_meanings.created_at','=',$id)
        ->delete();
        $item = App\definition_categories::distinct()
        ->where('definition_categories.created_at','=',$id)
        ->delete();
        $item = App\word_definitions::distinct()
        ->where('word_definitions.created_at','=',$id)
        ->delete();
        $item = App\words::distinct()
        ->where('words.created_at','=',$id)
        ->delete();
        $item = App\generic_judgments::distinct()
        ->where('generic_judgments.created_at','=',$id)
        ->delete();
    }
    public function edit($id)
    {
       
    }
    public function update(Request $request, $id)
    {
       
    }
    /*public function borrar()
    {
        $item = App\word2definitions::distinct()
        ->where('word2definitions.id_word2definition','>',0)
        ->delete();
        $item = App\word_audios::distinct()
        ->where('word_audios.id_word_audio','>',18)
        ->delete();
        $item = App\audio_judgments::distinct()
        ->where('audio_judgments.id_audio_judgment','>',18)
        ->delete();
        $item = App\definition_medias::distinct()
        ->where('definition_medias.id_definition_media','>',0)
        ->delete();
        $item = App\files::distinct()
        ->where('files.id_file','>',24)
        ->delete();
        $item = App\definition_meanings::distinct()
        ->where('definition_meanings.id_definition_meaning','>',0)
        ->delete();
        $item = App\definition_categories::distinct()
        ->where('definition_categories.id_definition_category','>',0)
        ->delete();
        $item = App\word_definitions::distinct()
        ->where('word_definitions.id_word_definition','>',21    )
        ->delete();
        $item = App\words::distinct()
        ->where('words.id_word','>',21)
        ->delete();
        $item = App\generic_judgments::distinct()
        ->where('generic_judgments.id_generic_judgment','>',666)
        ->delete();
//dd($id);
	   // Session::flash('message', $word2definitions['id_word2definition'] . ' deleted successfully');
	    return redirect('words/lote');
    }*/
    /*
    public function borrar2()
    {
        $item = App\word2definitions::distinct()
        ->where('word2definitions.id_word2definition','>',230)
        ->delete();
        $item = App\word_audios::distinct()
        ->where('word_audios.id_word_audio','>',402)
        ->delete();
        $item = App\audio_judgments::distinct()
        ->where('audio_judgments.id_audio_judgment','>',154)
        ->delete();
        $item = App\definition_medias::distinct()
        ->where('definition_medias.id_definition_media','>',70)
        ->delete();
        $item = App\files::distinct()
        ->where('files.id_file','>',255)
        ->delete();
        $item = App\definition_meanings::distinct()
        ->where('definition_meanings.id_definition_meaning','>',82)
        ->delete();
        $item = App\definition_categories::distinct()
        ->where('definition_categories.id_definition_category','>',82)
        ->delete();
        $item = App\word_definitions::distinct()
        ->where('word_definitions.id_word_definition','>',82)
        ->delete();
        $item = App\words::distinct()
        ->where('words.id_word','>',230)
        ->delete();
        $item = App\generic_judgments::distinct()
        ->where('generic_judgments.id_generic_judgment','>',676)
        ->delete();
//dd($id);
	   // Session::flash('message', $word2definitions['id_word2definition'] . ' deleted successfully');
	    return redirect('words/lote');
    }*/
    
}
