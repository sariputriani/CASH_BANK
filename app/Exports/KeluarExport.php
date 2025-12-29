<?php

namespace App\Exports;

use App\Models\bankKeluar;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class KeluarExport implements FromView
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('cash_bank.reportKeluar', [
            'data' => bankKeluar::all()
        ]);
    }
}
