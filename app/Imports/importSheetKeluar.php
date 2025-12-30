<?php

namespace App\Imports;

use App\Imports\importKeluar;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class importSheetKeluar implements WithMultipleSheets
{
    /**
    * @param Collection $collection
    */
    public function sheets():array
    {
        return [
            1 => new importKeluar()
        ];
    }
}
