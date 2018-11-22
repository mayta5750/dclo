<?php

namespace App\Imports;

use App\words;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
class WordsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new words([
           'id_word'     => $row[0],
           'grapheme'     => $row[1]

          // 'email'    => $row[1],
           //'password' => Hash :: make($row[2]),
        ]);
    }
}
