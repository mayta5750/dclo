<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\languages;

class LanguagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //public $mensaje='LISTADO DE IDIOMAS';
    public function index(Request $request)
    {
        $languages = App\languages::distinct()
        ->select('languages.language_name','languages.id_language')
        //->where('languages.id_language','!=',1)
        ->get();
       /* if(isset($men)!=null)
            dd($men);*/
        return view('languages.languages',compact('languages'))->with('mensaje','LISTADO DE IDIOMAS');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('languages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request([
            'language_name', 'description', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by','deleted_at'
        ]);
        $fecha = date("Y-m-d h:m:s");
        languages::create([
            'id_language' => $this->getIdLanguages(),
            'language_name' => strtoupper($data['language_name']),
            'description' => 'LENGUAJE '.strtoupper($data['language_name']),
            'created_by' => 0,
            'created_at' => $fecha,
            'updated_by' => 0,
            'updated_at' => $fecha,
            'deleted_by' => NULL,
            'deleted_at' => NULL
        ]);
        return redirect('languages');
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_language)
    {
        try {

            $item = App\languages::distinct()
            ->where('languages.id_language','=',$id_language)
            ->delete();
           // dd($id_language);
            return redirect('languages');
        
        }catch (\Illuminate\Database\QueryException $e){
            return redirect('languages')->with('men','nose');
        }

    }
    public function getIdLanguages(){
        $item = App\languages::distinct()
        ->orderby('id_language','DESC')->take(1)->get()->first();
        $id = $item['id_language'];
        return ($id+1);
    }
}
