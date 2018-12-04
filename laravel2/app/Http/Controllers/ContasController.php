<?php

namespace App\Http\Controllers;

use App\Conta;
use App\Contratos;
use App\Fornecedor;
use App\Mail\Aprovacao;
use App\Secretaria;
use App\Log;
use DateTime;
use App\AssignSecretaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Contestacao;
use PDFMerger;
use App\TokenAssina;
use Carbon\Carbon;

class ContasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissão=AssignSecretaria::where('idUsuario',\auth()->user()->id)
                         ->where('idSecretaria','1')
                         ->count();
        if($permissão >= 1 ) {
            $contratos = Contratos::all();
            return view('conta.create', compact('contratos'));
        }else{
            $ids=AssignSecretaria::where('idUsuario',\auth()->user()->id)
                                  ->select('idSecretaria')
                                  ->get();
            $contratos = Contratos::whereIn('idSecretaria',$ids)
                                    ->get();
            return view('conta.create', compact('contratos'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function createExtrato($id)
    {

        $contratos=Contratos::find($id);
        return view('conta.create',compact('contratos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'valorConta'=>'required|max:255',
            'vencimentoConta'=>'required',
            'extratopdf'=>'required',
            'NumeroBancario'=> 'required|int']);

        $filename=$request->get('NumeroBancario').'-'.$request->get('idContrato').'.pdf';
        $path = $request->file('extratopdf')->move(public_path() . '/pdf/', $filename );
        date_default_timezone_set('America/Sao_Paulo');
        $newExtrato= [
           'idContrato' => $request->get('idContrato'),
           'statusConta' => 'A',
           'valorConta' => $request->get('valorConta'),
           'vencimentoConta' => $request->get('vencimentoConta'),
           'pdfContaEndereco' => '/pdf/'.$filename,
           'criado_em' =>date('Y-m-d H:i'),
           'IdCriador'=> \auth()->user()->id,
            'NumeroBancario' => $request->get('NumeroBancario'),
        ];
            Conta::create($newExtrato);
        $log = [
            'tipoAcao'=>'Create',
            'objetoAcao'=>'Criou um novo extrato de numero bancario = '.$request->get('NumeroBancario'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
            return redirect()->route('contas.create')->with('success','Extrato Criado com sucesso');

    }

    function contasContratoContestaId($id,$idContrato){
        $contratos = Contratos::find($idContrato);
        $fornecedor = Fornecedor::find($contratos->idFornecedor);
        $conta = Conta::find($id);

        return view('contestacoes.contestacao',compact('conta','contratos','fornecedor'));

    }

    function contestacaoEnviar(Request $request){
        $this->validate($request,[
            'descricao'=> 'required',
            'email'=> 'required']);
/*
        var_dump($request->get('email'),$request->get('DescContrato'),$request->get('descricao'));
        die();
  */


            $users_temp = explode(',', $request->get('email'));
           // $teste = Mail::to($request->get('email'))
             $teste = Mail::to($users_temp)
            ->cc(\auth()->user()->email)
            ->send(new Contestacao($request->get('NumeroBancario'),$request->get('DescContrato'),$request->get('descricao')));
        $log = [
            'tipoAcao'=>'Update',
            'objetoAcao'=>'Contestou um extrato de numero bancario '.$request->get('NumeroBancario'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        return redirect()->route('contas.create')->with('success','Contestação enviada ao e-mail do fornecedor');
        //return view('contestacoes.contestacao',compact('conta','contratos','fornecedor'));

    }

    function contasContratoId($id){
        $contrato = Contratos::where('id',$id)
            ->take(1)
            ->get();
        $contas = Conta::where('idContrato',$id)
            ->paginate(5);
        $id_contrato = $id;


        return view('conta.search',compact('contas','contrato','id_contrato'))->with('i',(request()->input('page',1) -1) *5);;

    }


    function contasContratoall($tipo = 'A'){
        $permissão=AssignSecretaria::where('idUsuario',\auth()->user()->id)
            ->where('idSecretaria','1')
            ->count();
        if($permissão >= 1 ) {
            if($tipo == 'AP') {
                $secretarias = Secretaria::all();
                $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                    ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                    ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                    ->join('users', 'users.id', '=', 'tblContas.IdAprovador')
                    ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                        , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                        , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                        , 'tblContratos.descContrato', 'users.name')
                    ->where('tblContas.statusConta', $tipo)
                    ->paginate(10);
                }else{
                $secretarias = Secretaria::all();
                $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                    ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                    ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')

                    ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                        , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                        , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                        , 'tblContratos.descContrato')
                    ->where('tblContas.statusConta', $tipo)
                    ->paginate(10);
            }
        }else{
            $ids = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                ->select('idSecretaria')
                ->get();
            if($tipo == 'AP') {
                $secretarias = Secretaria::whereIn('id', $ids)
                    ->get();
                $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                    ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                    ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                    ->join('users', 'users.id', '=', 'tblContas.IdAprovador')
                    ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                        , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                        , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                        , 'tblContratos.descContrato', 'users.name')
                    ->where('tblContas.statusConta', $tipo)
                    ->whereIn('tblContratos.idSecretaria', $ids)
                    ->whereNotIn('tblContratos.idSecretaria', [1])
                    ->paginate(10);
            }else{
                $secretarias = Secretaria::whereIn('id', $ids)
                    ->get();
                $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                    ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                    ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                    ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                        , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                        , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                        , 'tblContratos.descContrato')
                    ->where('tblContas.statusConta', $tipo)
                    ->whereIn('tblContratos.idSecretaria', $ids)
                    ->whereNotIn('tblContratos.idSecretaria', [1])
                    ->paginate(10);
            }
        }
        return view('conta.searchall',compact('contas','tipo','secretarias'))->with('i',(request()->input('page',1) -1) *10);

    }





    /**
     * Display the specified resource.
     *
     * @param  \App\Conta  $conta
     * @return \Illuminate\Http\Response
     */
    public function show(Conta $conta)
    {
        //
    }

    public function searchFor(Request $request){



        if($request->get('type_date') == 'sd') {
            switch ($request->get('type')) {
                case 'nb':
                    switch ($request->get('type_date')) {

                        case 'dv':
                            $contrato = Contratos::where('id', $request->get('id_contrato'))
                                ->take(1)
                                ->get();
                            $contas = Conta::where('idContrato', $request->get('id_contrato'))
                                ->where('NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('statusConta', 'LIKE', $request->get('st_ex'))
                                ->paginate(5);

                            break;

                        case 'dc':
                            $contrato = Contratos::where('id', $request->get('id_contrato'))
                                ->take(1)
                                ->get();
                            $contas = Conta::where('idContrato', $request->get('id_contrato'))
                                ->where('NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('statusConta', 'LIKE', $request->get('st_ex'))
                                ->whereBetween('criado_em', [$request->get('date_in'), $request->get('date_fim')])
                                ->paginate(5);

                            break;

                        case 'da':
                            $contrato = Contratos::where('id', $request->get('id_contrato'))
                                ->take(1)
                                ->get();
                            $contas = Conta::where('idContrato', $request->get('id_contrato'))
                                ->where('NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('statusConta', 'LIKE', $request->get('st_ex'))
                                ->whereBetween('AprovadoEm', [$request->get('date_in'), $request->get('date_fim')])
                                ->paginate(5);

                            break;
                        default:
                            $contrato = Contratos::where('id', $request->get('id_contrato'))
                                ->take(1)
                                ->get();
                            $contas = Conta::where('idContrato', $request->get('id_contrato'))
                                ->where('NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('statusConta', 'LIKE', $request->get('st_ex'))
                                ->paginate(5);

                            break;
                    }

                    break;
            }
        }else{

            $this->validate($request,[
                'date_in'=>'required',
                'date_fim'=>'required']);
            $datain = DateTime::createFromFormat('d/m/Y', $request->get('date_in'))->format('Y-m-d');
            $datafim = DateTime::createFromFormat('d/m/Y', $request->get('date_fim'))->format('Y-m-d');




            switch ($request->get('type')) {
                case 'nb':
                    switch ($request->get('type_date')) {

                        case 'dv':
                            $contrato = Contratos::where('id', $request->get('id_contrato'))
                                ->take(1)
                                ->get();
                            $contas = Conta::where('idContrato', $request->get('id_contrato'))
                                ->where('NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('statusConta', 'LIKE', $request->get('st_ex'))
                                ->whereBetween('vencimentoConta', [$datain, $datafim])
                            ->paginate(5);

                            break;

                        case 'dc':
                            $contrato = Contratos::where('id', $request->get('id_contrato'))
                                ->take(1)
                                ->get();
                            $contas = Conta::where('idContrato', $request->get('id_contrato'))
                                ->where('NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('statusConta', 'LIKE', $request->get('st_ex'))
                                ->whereBetween('criado_em', [$datain, $datafim])
                                ->paginate(5);

                            break;

                        case 'da':
                            $contrato = Contratos::where('id', $request->get('id_contrato'))
                                ->take(1)
                                ->get();
                            $contas = Conta::where('idContrato', $request->get('id_contrato'))
                                ->where('NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('statusConta', 'LIKE', $request->get('st_ex'))
                                ->whereBetween('AprovadoEm', [$datain, $datafim])
                                ->paginate(5);

                            break;
                        default:
                            $contrato = Contratos::where('id', $request->get('id_contrato'))
                                ->take(1)
                                ->get();
                            $contas = Conta::where('idContrato', $request->get('id_contrato'))
                                ->where('NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('statusConta', 'LIKE', $request->get('st_ex'))
                                ->paginate(5);

                            break;
                    }

                    break;
            }
        }
    

    
    
    
    
        return view('conta.search',compact('contas','contrato','id_contrato'))
            ->with('id',$request->get('id_contrato'))
            ->with('i',(request()->input('page',1) -1) *5);

    }



//
    public function searchForall(Request $request)
    {

        if ($request->get('type_date') == 'sd') {

            /*Sw case responsavel por filtar o tipo de dado enviado
            nb = numero bancario
            for = fornecedor
            */
            switch ($request->get('type')) {

                case 'nb':
                    $permissão = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                        ->where('idSecretaria', '1')
                        ->count();
                    if ($permissão >= 1) {
                        if($request->get('st_ex') == 'AP'){
                            $secretarias = Secretaria::all();
                            $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                ->join('users','tblContas.idAprovador','=','users.id')
                                ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                    , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                    , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                    , 'tblContratos.descContrato','users.name')
                                ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                ->paginate(5);


                        }else {
                            $secretarias = Secretaria::all();
                            $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                    , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                    , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                    , 'tblContratos.descContrato')
                                ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                ->paginate(5);
                        }
                    } else {
                        $ids = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                            ->select('idSecretaria')
                            ->get();
                        if($request->get('st_ex') == 'AP') {
                            $secretarias = Secretaria::whereIn('id', $ids)
                                ->get();
                            $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                    , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                    , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                    , 'tblContratos.descContrato')
                                ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->whereIn('tblContratos.idSecretaria', $ids)
                                ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                ->paginate(5);
                        }else{
                            $secretarias = Secretaria::whereIn('id', $ids)
                                ->get();
                            $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                ->join('users','tblContas.idAprovador','=','users.id')
                                ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                    , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                    , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                    , 'tblContratos.descContrato','users.name')
                                ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                ->whereIn('tblContratos.idSecretaria', $ids)
                                ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                ->paginate(5);
                        }
                    }
                case 'forn':
                    $permissão = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                        ->where('idSecretaria', '1')
                        ->count();
                    if ($permissão >= 1) {
                        if($request->get('st_ex') == 'AP') {
                            $secretarias = Secretaria::all();
                            $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                ->join('users','tblContas.idAprovador','=','users.id')
                                ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                    , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                    , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                    , 'tblContratos.descContrato','users.name')
                                ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                ->paginate(5);
                        }else{
                            $secretarias = Secretaria::all();
                            $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                    , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                    , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                    , 'tblContratos.descContrato')
                                ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                ->paginate(5);
                        }
                    } else {
                        $ids = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                            ->select('idSecretaria')
                            ->get();
                        if($request->get('st_ex') == 'AP') {
                            $secretarias = Secretaria::whereIn('id', $ids)
                                ->get();
                            $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                ->join('users','tblContas.idAprovador','=','users.id')
                                ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                    , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                    , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                    , 'tblContratos.descContrato','users.name')
                                ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                ->whereIn('tblContratos.idSecretaria', $ids)
                                ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                ->paginate(5);
                        }else{
                            $secretarias = Secretaria::whereIn('id', $ids)
                                ->get();
                            $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                    , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                    , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                    , 'tblContratos.descContrato')
                                ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                ->whereIn('tblContratos.idSecretaria', $ids)
                                ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                ->paginate(5);
                        }
                    }
                    break;
            }
            /*Fim dos sw case*/

        }else{

            /*caso tenha as datas no parametro ele filtra*/
/*
                $this->validate($request, [
                    'date_in' => 'required',
                    'date_fim' => 'required']);
*/
                if(is_null($request->get('date_in')) or is_null($request->get('date_fim')) ){
                    $datain = date("Y-m-d");
                    $datafim =date("Y-m-d");
                }else {
                  $datain = DateTime::createFromFormat('Y-m-d', $request->get('date_in'))->format('Y-m-d');
                  $datafim = DateTime::createFromFormat('Y-m-d', $request->get('date_fim'))->format('Y-m-d');
                }
                switch ($request->get('type')) {
                case 'nb':
                    switch ($request->get('type_date')){
                    case 'dv':
                                $permissão = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                    ->where('idSecretaria', '1')
                                    ->count();
                                if ($permissão >= 1) {
                                    if($request->get('st_ex') == 'AP') {
                                        $secretarias = Secretaria::all();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->join('users','tblContas.idAprovador','=','users.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato','users.name')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.vencimentoConta', [$datain, $datafim])
                                            ->paginate(5);
                                    }else{
                                        $secretarias = Secretaria::all();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.vencimentoConta', [$datain, $datafim])
                                            ->paginate(5);
                                    }
                                } else {
                                    $ids = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                        ->select('idSecretaria')
                                        ->get();
                                    $secretarias = Secretaria::whereIn('id', $ids)
                                        ->get();
                                    $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                        ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                        ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                        ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                            , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                            , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                            , 'tblContratos.descContrato')
                                        ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                        ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                        ->whereIn('tblContratos.idSecretaria', $ids)
                                        ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                        ->whereBetween('tblContas.vencimentoConta', [$datain, $datafim])
                                        ->paginate(5);
                                }
                    break;
                    case 'dc':
                                $permissão = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                    ->where('idSecretaria', '1')
                                    ->count();
                                if ($permissão >= 1) {
                                    if($request->get('st_ex') == 'AP'){
                                    $secretarias = Secretaria::all();
                                    $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                        ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                        ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                        ->join('users','tblContas.idAprovador','=','users.id')
                                        ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                            , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                            , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                            , 'tblContratos.descContrato','users.name')
                                        ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                        ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                        ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                        ->whereBetween('tblContas.criado_em', [$datain, $datafim])
                                        ->paginate(5);
                                    }else{
                                        $secretarias = Secretaria::all();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.criado_em', [$datain, $datafim])
                                            ->paginate(5);
                                    }
                                } else {
                                    $ids = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                        ->select('idSecretaria')
                                        ->get();
                                    if($request->get('st_ex') == 'AP') {
                                        $secretarias = Secretaria::whereIn('id', $ids)
                                            ->get();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->join('users','tblContas.idAprovador','=','users.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato','users.name')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->whereIn('tblContratos.idSecretaria', $ids)
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.criado_em', [$datain, $datafim])
                                            ->paginate(5);
                                    }else{
                                        $secretarias = Secretaria::whereIn('id', $ids)
                                            ->get();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->whereIn('tblContratos.idSecretaria', $ids)
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.criado_em', [$datain, $datafim])
                                            ->paginate(5);
                                    }
                                }
                    break;
                    case 'da':
                                $permissão = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                    ->where('idSecretaria', '1')
                                    ->count();
                                if ($permissão >= 1) {
                                    if($request->get('st_ex') == 'AP') {
                                        $secretarias = Secretaria::all();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->join('users','tblContas.idAprovador','=','users.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato','users.name')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.AprovadoEm', [$datain, $datafim])
                                            ->paginate(5);
                                    }else{
                                        $secretarias = Secretaria::all();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.AprovadoEm', [$datain, $datafim])
                                            ->paginate(5);
                                    }
                                } else {
                                    $ids = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                        ->select('idSecretaria')
                                        ->get();
                                    if($request->get('st_ex') == 'AP') {
                                        $secretarias = Secretaria::whereIn('id', $ids)
                                            ->get();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->join('users','tblContas.idAprovador','=','users.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato','users.name')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->whereIn('tblContratos.idSecretaria', $ids)
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.AprovadoEm', [$datain, $datafim])
                                            ->paginate(5);
                                    }else{
                                        $secretarias = Secretaria::whereIn('id', $ids)
                                            ->get();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblContas.NumeroBancario', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->whereIn('tblContratos.idSecretaria', $ids)
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.AprovadoEm', [$datain, $datafim])
                                            ->paginate(5);
                                    }
                                  }
                    break;
                    }
                break;
                case 'forn':
                        switch ($request->get('type_date')){
                        case 'dv':
                                $permissão = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                    ->where('idSecretaria', '1')
                                    ->count();
                                if ($permissão >= 1) {
                                    if($request->get('st_ex') == 'AP') {
                                        $secretarias = Secretaria::all();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->join('users','tblContas.idAprovador','=','users.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato','users.name')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.vencimentoConta', [$datain, $datafim])
                                            ->paginate(5);
                                    }else{
                                        $secretarias = Secretaria::all();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.vencimentoConta', [$datain, $datafim])
                                            ->paginate(5);
                                    }
                                } else {
                                    $ids = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                        ->select('idSecretaria')
                                        ->get();

                                    if($request->get('st_ex') == 'AP') {
                                        $secretarias = Secretaria::whereIn('id', $ids)
                                            ->get();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->join('users','tblContas.idAprovador','=','users.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato','users.name')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->whereIn('tblContratos.idSecretaria', $ids)
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.vencimentoConta', [$datain, $datafim])
                                            ->paginate(5);
                                       }else{
                                        $secretarias = Secretaria::whereIn('id', $ids)
                                            ->get();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->whereIn('tblContratos.idSecretaria', $ids)
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.vencimentoConta', [$datain, $datafim])
                                            ->paginate(5);
                                    }
                                }
                        break;
                        case 'dc':
                                $permissão = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                    ->where('idSecretaria', '1')
                                    ->count();
                                if ($permissão >= 1) {
                                    if($request->get('st_ex') == 'AP') {
                                        $secretarias = Secretaria::all();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->join('users','tblContas.idAprovador','=','users.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato','users.name')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.criado_em', [$datain, $datafim])
                                            ->paginate(5);
                                    }else{
                                        $secretarias = Secretaria::all();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.criado_em', [$datain, $datafim])
                                            ->paginate(5);
                                    }
                                } else {
                                    $ids = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                        ->select('idSecretaria')
                                        ->get();
                                    if($request->get('st_ex') == 'AP') {
                                        $secretarias = Secretaria::whereIn('id', $ids)
                                            ->get();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->join('users','tblContas.idAprovador','=','users.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato','users.name')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->whereIn('tblContratos.idSecretaria', $ids)
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.criado_em', [$datain, $datafim])
                                            ->paginate(5);
                                    }else{
                                        $secretarias = Secretaria::whereIn('id', $ids)
                                            ->get();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->whereIn('tblContratos.idSecretaria', $ids)
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.criado_em', [$datain, $datafim])
                                            ->paginate(5);
                                    }
                                }
                        break;
                        case 'da':
                                $permissão = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                    ->where('idSecretaria', '1')
                                    ->count();
                                if ($permissão >= 1) {
                                    if($request->get('st_ex') == 'AP') {
                                        $secretarias = Secretaria::all();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->join('users','tblContas.idAprovador','=','users.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato','users.name')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.AprovadoEm', [$datain, $datafim])
                                            ->paginate(5);
                                    }else{
                                        $secretarias = Secretaria::all();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.AprovadoEm', [$datain, $datafim])
                                            ->paginate(5);
                                    }
                                } else {
                                    $ids = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                                        ->select('idSecretaria')
                                        ->get();
                                    if($request->get('st_ex') == 'AP') {
                                        $secretarias = Secretaria::whereIn('id', $ids)
                                            ->get();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->join('users','tblContas.idAprovador','=','users.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato','users.name')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->whereIn('tblContratos.idSecretaria', $ids)
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.AprovadoEm', [$datain, $datafim])
                                            ->paginate(5);
                                    }else{
                                        $secretarias = Secretaria::whereIn('id', $ids)
                                            ->get();
                                        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
                                            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                                            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                                            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                                                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                                                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                                                , 'tblContratos.descContrato')
                                            ->where('tblContas.statusConta', 'LIKE', $request->get('st_ex'))
                                            ->where('tblFornecedor.nomeFantasia', 'LIKE', "%" . $request->get('valor') . "%")
                                            ->whereIn('tblContratos.idSecretaria', $ids)
                                            ->where('tblContratos.idSecretaria', 'LIKE', "%" . $request->get('sec') . "%")
                                            ->whereBetween('tblContas.AprovadoEm', [$datain, $datafim])
                                            ->paginate(5);
                                    }
                                }
                        break;
                             }
                break;

                }

            }

  
  
  
  

            return view('conta.searchall', compact('contas',  'secretarias'))->with('tipo',$request->get('st_ex'))
                ->with('i',(request()->input('page',1) -1) *5);

    }



 /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Conta  $conta
     * @return \Illuminate\Http\Response
     */
    public function edit(Conta $conta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Conta  $conta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Conta $conta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Conta  $conta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conta $conta)
    {
        //
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $update = [
            'statusConta' => 'IN',
            'ModificadoEm' =>date('Y-m-d H:i'),
            'IdModificador'=> \auth()->user()->id,

        ];
        Conta::whereIn('id',explode(",",$ids))->update($update);
        $log = [
            'tipoAcao'=>'Delete',
            'objetoAcao'=>'Deletou os extratos de id ='.$ids,
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
       // return redirect()->route('contas.create')->with('success','Extrato Criado com sucesso');
        return response()->json(['success'=>"Invalidado com sucesso."]);
    }

    public function invalidate($id,$id_contrato)
    {
        $update = [
            'statusConta' => 'IN',
            'ModificadoEm' =>date('Y-m-d H:i'),
            'IdModificador'=> \auth()->user()->id,
        ];
        Conta::find($id)->update($update);
        $log = [
            'tipoAcao'=>'Update',
            'objetoAcao'=>'invalidou o extrato id ='.$id,
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        // return redirect()->route('contas.create')->with('success','Extrato Criado com sucesso');
        return redirect()->route('contas.contasContratoId',$id_contrato)->with('success','Extrato invalidado com sucesso');
    }

    public function reeopen($id,$id_contrato)
    {
        $update = [
            'statusConta' => 'A',
            'ModificadoEm' =>date('Y-m-d H:i'),
            'IdModificador'=> \auth()->user()->id,
            'IdAprovador'=> 0,
            'AprovadoEm'=> Null,
        ];
        Conta::find($id)->update($update);
        $log = [
            'tipoAcao'=>'Update',
            'objetoAcao'=>'Reabriu extrato de id ='.$id,
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        // return redirect()->route('contas.create')->with('success','Extrato Criado com sucesso');
      //  return redirect()->route('contas.contasContratoId',$id_contrato)->with('success','Extrato Reaberto com sucesso');
        return redirect()->back()->with('success','Extrato Reaberto com sucesso');
    }

    public function sendMailAprove($id){

        $contas = Conta::join('tblContratos', 'tblContratos.id', '=', 'tblContas.idContrato')
            ->join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
            ->select('tblContas.id', 'tblContas.statusConta', 'tblContas.valorConta', 'tblContas.NumeroBancario'
                , 'tblContas.vencimentoConta', 'tblContas.criado_em', 'tblContas.idContestacao'
                , 'tblContas.idContrato', 'tblSecretaria.descSecretaria', 'tblFornecedor.nomeFantasia'
                , 'tblContratos.descContrato','tblFornecedor.emailForncedor')
            ->where('tblContas.id',$id)
            ->take(1)
            ->get();


        $teste = Mail::to($contas[0]->emailForncedor)
                 ->cc(\auth()->user()->email)
                 ->send(new Aprovacao($contas[0]->NumeroBancario,$contas[0]->DescContrato));

    }


    public function aprove($id,$id_contrato,Request $request)
    {
        $update = [
            'statusConta' => 'AP',
            'ModificadoEm' =>date('Y-m-d H:i'),
            'IdModificador'=> \auth()->user()->id,
            'AprovadoEm' =>date('Y-m-d H:i'),
            'IdAprovador'=> \auth()->user()->id
        ];
        Conta::find($id)->update($update);

        $info_aprovav = [
            'idConta' =>$id,
            'Navegador'=>\Browser::browserName(),
            'SO'=>\Browser::platformName(),
            'IP'=>\Request::ip(),
            'Token'=>'ITU'.Carbon::now()->timestamp.''.$id,
        ];

        TokenAssina::create($info_aprovav);
        $log = [
            'tipoAcao'=>'Update',
            'objetoAcao'=>'Aprovou extrato de id ='.$id,
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        // return redirect()->route('contas.create')->with('success','Extrato Criado com sucesso');
        //$this->sendMailAprove($id);
       // return redirect()->route('contas.contasContratoId',$id_contrato)->with('success','Extrato aprovado com sucesso');
        return redirect()->back()->with('success','Extrato aprovado com sucesso');
    }




   public function aproveAll(Request $request)
   {

       $ids = $request->ids;
       $id_array = explode(",", $ids);
       $update = [
           'statusConta' => 'AP',
           'ModificadoEm' => date('Y-m-d H:i'),
           'IdModificador' => \auth()->user()->id,
           'AprovadoEm' => date('Y-m-d H:i'),
           'IdAprovador' => \auth()->user()->id
       ];

       foreach ($id_array as &$contas) {

           $info_aprovav = [
               'idConta' => $contas,
               'Navegador' => \Browser::browserName(),
               'SO' => \Browser::platformName(),
               'IP' => \Request::ip(),
               'Token' => 'ITU' . Carbon::now()->timestamp . '' . $contas,
           ];

           TokenAssina::create($info_aprovav);
          // $this->sendMailAprove($contas);
       }

       Conta::whereIn('id', $id_array)->update($update);
       $log = [
           'tipoAcao'=>'Update',
           'objetoAcao'=>'Aprovou grupo de extratos de ids ='.$ids,
           'idUsuarioAcao' => \auth()->user()->id,
           'dataAcao'=>date('Y-m-d H:i'),
       ];

       Log::create($log);
       return response()->json(['success' => "Todos aprovados com sucesso."]);

   }
}
