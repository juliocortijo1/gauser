<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\PDF;
use App\Conta;
use DateTime;
use App\User;
use App\Auth;
use File;
use App\Log;
use App\AssinaturaConfiguracoes;

class AssinaturaConfiguracoesController extends Controller
{

    public function index()
    {
        $assinatura = AssinaturaConfiguracoes::where('idUsuario',\auth()->user()->id)
                                ->take(1)
                                ->get();
        return view('usuario.assinatura',compact('assinatura'));
    }
    public function enviar(Request $request)
    {

        // Obtém dados do usuário logado
        $usuario = User::find(\auth()->user()->id);

        // Valida tipo arquivo importado - Tamanho Máximo: 10 Mb
        if(null !==($request->file('arquivo_assinatura'))){
            $this->validate($request,['arquivo_assinatura' => 'required | mimes:jpeg,jpg,png| max:10000']);
            // Salva arquivo no servidor
            $arquivo = $request->file('arquivo_assinatura');
            $arquivo->move(
                public_path().'/assinaturas/',
                $usuario['id'] . '.' . $arquivo->getClientOriginalExtension()
            );

            $conf_usuario =[
                    'idUsuario' => $usuario['id'],
                    'assinatura' => '/assinaturas/' . $usuario['id'] . '.' . $arquivo->getClientOriginalExtension(),
                    'cpf' => $request->get('cpf')
                ];
        }else{
            $conf_usuario =    [
                'idUsuario' => $usuario['id'],
                'cpf' => $request->get('cpf')
            ];
        }
        // Salva infos no banco de dados
        AssinaturaConfiguracoes::updateOrCreate(['idUsuario' => \auth()->user()->id],$conf_usuario);

        $log = [
        'tipoAcao'=>'Update',
        'objetoAcao'=>'Atualizou informações de assinatura',
        'idUsuarioAcao' => \auth()->user()->id,
        'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        return redirect()->route('usuarios.assinatura')->with('success', 'Assinatura anexada com sucesso!');
    }

    public function Create_PDF_Aprove($conta_id){
        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
            ->join('users', 'users.id', '=', 'tblContas.IdAprovador')
            ->join('tblAssinaturaConf', 'users.id', '=','tblAssinaturaConf.idUsuario')
            ->join('tblToken', 'tblToken.idConta', '=', 'tblContas.id')
            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                , 'tblContas.vencimentoConta', 'tblContas.criado_em','tblContas.idContrato', 'tblSecretaria.descSecretaria'
                , 'tblContas.AprovadoEm', 'tblContratos.descContrato','tblContas.pdfContaEndereco'
                , 'users.name','tblAssinaturaConf.cpf','tblAssinaturaConf.assinatura','tblToken.token')
            ->where('tblContas.id', $conta_id)
            ->take(1)
            ->get();


        $pdfFile1Path = public_path().$contas[0]->pdfContaEndereco;
        $arquivo=\PDF::loadView('templates.assinatura', compact('contas'));
        $arquivo->setPaper('a4');
        $arquivo->save(public_path().'/pdf/assinatura-'.$contas[0]->NumeroBancario.''.$contas[0]->id.''.$contas[0]->statusConta.''.\auth()->user()->id.'.pdf');
        $pdfFile2Path = public_path().'/pdf/assinatura-'.$contas[0]->NumeroBancario.''.$contas[0]->id.''.$contas[0]->statusConta.''.\auth()->user()->id.'.pdf';
        $pdf = new \PDFMerger;

        $pdf->addPDF($pdfFile1Path, 'all');
        $pdf->addPDF($pdfFile2Path, 'all');

        //File::delete($pdfFile2Path);
        return $pdf->merge('download', $contas[0]->NumeroBancario.'-'.$contas[0]->idContrato.'.pdf');

    }
}
