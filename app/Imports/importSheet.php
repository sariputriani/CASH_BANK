<?php

namespace App\Imports;

use App\Imports\importMasuk;
use App\Imports\importKeluar;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class importSheet implements WithMultipleSheets
{
    /**
    * @param Collection $collection
    */
    public function sheets():array
    {
        return [
            0 => new importMasuk()
        ];
    }
}
