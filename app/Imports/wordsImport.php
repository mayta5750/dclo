<?php

namespace App\Imports;

use App\words;
use App\generic_judgments;

use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Imports\YourImport;
use Maatwebsite\Excel\Importer;
use App\User;
use Maatwebsite\Excel\Concerns\Importable;

class wordsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    use Importable;

    public function model(array $row)
    {
        generic_judgments::create([
            //'id_generic_judgment' => $data['id_generic_judgment'],
             'cat_data_type' => $row[0],
             'created_by'=>0
         ]);
         words::create([
            //'id_word'=>$i,
            'grapheme' => $row[0]
         ]);
         //"INSERT INTO `generic_judgments` (`id_generic_judgment`, `cat_data_type`, `cat_state`, `obs`, `created_by`, `created_at`, `updated_by`, `updated_at`, `deleted_by`, `deleted_at`) VALUES \n";

         



    }
    public function model1(array $row)
    {

        return new generic_judgments([
            'cat_data_type' => $row[0],
            'created_by'=>0,
        ]);


    }
    
   
}
