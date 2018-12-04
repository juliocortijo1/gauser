<?php

namespace App\Http\Controllers;

use App\Contratos;
use App\Fornecedor;
use App\Secretaria;
use App\Conta;
use App\AssignSecretaria;
use Illuminate\Http\Request;
use App\Log;
class ContratosController extends Controller
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
            $contratos = Contratos::join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                ->select('tblContratos.id', 'tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt', 'tblContratos.statusContrato', 'tblSecretaria.descSecretaria')
                ->paginate(5);
        }else{
            $ids=AssignSecretaria::where('idUsuario',\auth()->user()->id)
                ->select('idSecretaria')
                ->get();
            $contratos = Contratos::join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                ->select('tblContratos.id', 'tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt', 'tblContratos.statusContrato', 'tblSecretaria.descSecretaria')
                ->whereIn('tblSecretaria.id',$ids)
                ->paginate(5);
        }
        return view('contrato.index', compact('contratos'))->with('i',(request()->input('page',1) -1) *5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fornecedores = Fornecedor::all();
        $permissão=AssignSecretaria::where('idUsuario',\auth()->user()->id)
        ->where('idSecretaria','1')
        ->count();
        if($permissão >= 1 ) {
            $secretarias = Secretaria::all();
        }else{
            $ids=AssignSecretaria::where('idUsuario',\auth()->user()->id)
                ->select('idSecretaria')
                ->get();

            $secretarias = Secretaria::whereIn('id',$ids)
                ->get();
        }

        return view('contrato.create',compact('fornecedores','secretarias'));
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
            'descContrato'=>'required|string|max:100',
            'identContratoExt'=>'required|string|max:100']);
            Contratos::create($request->all());
        $log = [
            'tipoAcao'=>'Create',
            'objetoAcao'=>'Criou o contrato  ='.$request->get('descContrato'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
            return redirect()->route('contratos.index')->with('success','Contrato Cadastrado com sucesso');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $permissão=AssignSecretaria::where('idUsuario',\auth()->user()->id)
            ->where('idSecretaria','1')
            ->count();

        if($permissão >= 1  ) {
        $contratos = Contratos::join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
            ->select('tblContratos.id','tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt','tblContratos.statusContrato','tblSecretaria.descSecretaria')
            ->paginate(5);
        }else{
            $ids=AssignSecretaria::where('idUsuario',\auth()->user()->id)
                ->select('idSecretaria')
                ->get();
            $contratos = Contratos::join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                ->select('tblContratos.id','tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt','tblContratos.statusContrato','tblSecretaria.descSecretaria')
                ->whereIn('tblSecretaria.id',$ids)
                ->paginate(5);
        }
        return  view('contrato.search',compact('contratos'))->with('i',(request()->input('page',1) -1) *5);
    }

    public function CreateExtratoContrato()
    {
        $permissão=AssignSecretaria::where('idUsuario',\auth()->user()->id)
            ->where('idSecretaria','1')
            ->count();

        if($permissão >= 1  ) {
        $contratos = Contratos::join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
            ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
            ->select('tblContratos.id','tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt','tblContratos.statusContrato','tblSecretaria.descSecretaria')
            ->get();
           // ->paginate(5);
        }else {
            $ids = AssignSecretaria::where('idUsuario', \auth()->user()->id)
                ->select('idSecretaria')
                ->get();
            $contratos = Contratos::join('tblFornecedor', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                ->select('tblContratos.id', 'tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt', 'tblContratos.statusContrato', 'tblSecretaria.descSecretaria')
                ->whereIn('tblSecretaria.id', $ids)
                ->get();
               // ->paginate(5);
        }
        return  view('contrato.searchCreate',compact('contratos'))->with('i',(request()->input('page',1) -1) *5);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fornecedores = Fornecedor::all();
        $secretarias = Secretaria::all();
        $contrato = Contratos::find($id);
        return view('contrato.edit',compact('contrato','fornecedores','secretarias'));
    }

    public function searchFor(Request $request)
    {
        switch($request->get('type'))
        {
            case 'f':
                $contratos = Fornecedor::join('tblContratos', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                    ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                    ->select('tblContratos.id','tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt','tblContratos.statusContrato','tblSecretaria.descSecretaria')
                    ->where('tblFornecedor.nomeFantasia','LIKE',"%".$request->get('valor')."%")
                    ->paginate(5);
                break;
            case 's':
                $contratos = Fornecedor::join('tblContratos', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                ->select('tblContratos.id','tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt','tblContratos.statusContrato','tblSecretaria.descSecretaria')
                ->where('tblSecretaria.descSecretaria','LIKE',"%".$request->get('valor')."%")
                    ->paginate(5);
            break;
            case 'd':
                $contratos = Fornecedor::join('tblContratos', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                    ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                    ->select('tblContratos.id','tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt','tblContratos.statusContrato','tblSecretaria.descSecretaria')
                    ->where('tblContratos.descContrato','LIKE',"%".$request->get('valor')."%")
                    ->paginate(5);
                break;
            case 'ie':
                $contratos = Fornecedor::join('tblContratos', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                    ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                    ->select('tblContratos.id','tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt','tblContratos.statusContrato','tblSecretaria.descSecretaria')
                    ->where('tblContratos.identContratoExt','LIKE',"%".$request->get('valor')."%")
                    ->paginate(5);
                break;
        }
        return  view('contrato.search',compact('contratos'))->with('i',(request()->input('page',1) -1) *5);
    }

    public function searchForToExtrato(Request $request)
    {
        switch($request->get('type'))
        {
            case 'f':
                $contratos = Fornecedor::join('tblContratos', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                    ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                    ->select('tblContratos.id','tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt','tblContratos.statusContrato','tblSecretaria.descSecretaria')
                    ->where('tblFornecedor.nomeFantasia','LIKE',"%".$request->get('valor')."%")
                    ->paginate(5);
                break;
            case 's':
                $contratos = Fornecedor::join('tblContratos', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                    ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                    ->select('tblContratos.id','tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt','tblContratos.statusContrato','tblSecretaria.descSecretaria')
                    ->where('tblSecretaria.descSecretaria','LIKE',"%".$request->get('valor')."%")
                    ->paginate(5);
                break;
            case 'd':
                $contratos = Fornecedor::join('tblContratos', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                    ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                    ->select('tblContratos.id','tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt','tblContratos.statusContrato','tblSecretaria.descSecretaria')
                    ->where('tblContratos.descContrato','LIKE',"%".$request->get('valor')."%")
                    ->paginate(5);
                break;
            case 'ie':
                $contratos = Fornecedor::join('tblContratos', 'tblContratos.idFornecedor', '=', 'tblFornecedor.id')
                    ->join('tblSecretaria', 'tblContratos.idSecretaria', '=', 'tblSecretaria.id')
                    ->select('tblContratos.id','tblFornecedor.nomeFantasia', 'tblContratos.descContrato', 'tblContratos.identContratoExt','tblContratos.statusContrato','tblSecretaria.descSecretaria')
                    ->where('tblContratos.identContratoExt','LIKE',"%".$request->get('valor')."%")
                    ->paginate(5);
                break;
        }
        return  view('contrato.searchCreate',compact('contratos'))->with('i',(request()->input('page',1) -1) *5);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request,[
            'descContrato'=>'required|string|max:100',
            'identContratoExt'=>'required|string|max:100']);
        Contratos::find($request->get('idContrato'))->update($request->all());
        $log = [
            'tipoAcao'=>'Update',
            'objetoAcao'=>'Atualizou contrato = '.$request->get('descContrato'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        return redirect()->route('contratos.index')->with('success','Contrato Atualizado com Sucesso');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $InfoContrato = Contratos::find($request->get('idContrato'));
        Contratos::find($request->get('idContrato'))
                   ->delete();
        //Conta::where('idContrato',$request->get('idContrato'))->delete();
        $log = [
            'tipoAcao'=>'Delete',
            'objetoAcao'=>'deletou o contrato id='.$request->get('idContrato').' Desc Contrato = '.$InfoContrato->descContrato.' Identificacao Externa = '.$InfoContrato->identContratoExt,
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
       return redirect()->route('contratos.index')->with('success','Contrato removido com sucesso');
    }

}
