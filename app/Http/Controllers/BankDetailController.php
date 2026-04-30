<?php

namespace App\Http\Controllers;

use App\Imports\BankDetailsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

class BankDetailController extends Controller
{
    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv',
    ]);
   
    FacadesExcel::import(new BankDetailsImport, $request->file('file'));

    return back()->with('success', 'Bank details imported successfully.');
}
}
