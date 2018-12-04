<?php

namespace App\Http\Controllers;

use App\Contratos;
use Illuminate\Http\Request;

class AutocompleteController extends Controller
{
    public function autocompleteContrato(Request $request)
    {
        $data = Contratos::select("descContrato")
            ->where("descContrato","LIKE","%{$request->input('query')}%")
            ->get();

        return response()->json($data);
    }
}
