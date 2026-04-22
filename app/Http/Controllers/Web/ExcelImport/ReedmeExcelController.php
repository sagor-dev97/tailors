<?php

namespace App\Http\Controllers\Web\ExcelImport;

use Illuminate\Http\Request;
use App\Exports\ReedmeCodeExport;
use App\Imports\ReedmeCodeImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ReedmeExcelController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new ReedmeCodeImport, $request->file('file'));

        return back()->with('t-success', 'Reedme Codes Imported Successfully!');
    }


    public function export()
    {
        return Excel::download(new ReedmeCodeExport, 'reedme_codes.xlsx');
    }
}
