<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TokenAssina;

class TokenController extends Controller
{
    public function valida(Request $request)
    {

        $tokens = TokenAssina::join('tblContas','tblContas.id','=','tblToken.idConta')
                  ->join('users', 'tblContas.idAprovador', '=', 'users.id')
                  ->join('tblAssinaturaConf', 'users.id', '=', 'tblAssinaturaConf.idUsuario')
                  ->select('tblContas.NumeroBancario','tblContas.AprovadoEm','tblToken.Navegador','tblToken.SO','tblToken.IP','tblToken.Token','users.name','tblAssinaturaConf.cpf')
                  ->where('tblToken.Token',$request->get('info'))
                  ->take(1)
                  ->get();
        return  view('conta.searchtoken',compact('tokens'));
    }
}
