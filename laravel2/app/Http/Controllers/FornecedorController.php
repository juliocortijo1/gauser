<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fornecedor;
use App\Contratos;
use App\User;
use App\Log;
class FornecedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fornecedores = Fornecedor::where('tipoPessoa','<>','I')
                        ->latest()->paginate(5);
        return view('fornecedor.index', compact('fornecedores'))->with('i',(request()->input('page',1) -1) *5);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('fornecedor.create');
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
             'nomeFantasia'=>'required|string|max:255'
            , 'emailForncedor'=>'required|string|email|max:255|unique:tblFornecedor'
            , 'passFornecedor'=>'required|string|min:6'
            ,'attributes' => [
                 'nomeFantasia' => 'Nome Fantasia'
             ]
        ]);
        Fornecedor::create($request->all());
        $log = [
            'tipoAcao'=>'Create',
            'objetoAcao'=>'Criou o Fornecedor = '.$request->get('nomeFantasia'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);

        $infouser= array(
            'name' => $request->get('nomeFantasia'),
            'email' => $request->get('emailForncedor'),
            'password' => bcrypt($request->get('passFornecedor')),
            'permissao'=>3,
            'tipo' =>'F'

        );
        User::create($infouser);
        $log = [
            'tipoAcao'=>'Create',
            'objetoAcao'=>'Processo criação de fornecedor criou usuario automaticamente  = '.$request->get('emailForncedor'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);

        return redirect()->route('fornecedor.index')->with('success','Fornecedor Cadastrado com sucesso');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fornecedores = Fornecedor::where('tipoPessoa','<>','I')
                        ->latest()->paginate(5);
        return  view('fornecedor.search',compact('fornecedores'))->with('i',(request()->input('page',1) -1) *5);
    }

    /**
     * searchFornecedor a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchFor(Request $request)
    {
        switch($request->get('type'))
        {
            case 'n':
              $fornecedores = Fornecedor::where('nomeFantasia','LIKE',"%".$request->get('valor')."%")
                                        ->where('tipoPessoa','<>','I')
                                        ->get();
            break;
            case 'e':
              $fornecedores = Fornecedor::where('emailForncedor','LIKE',"%".$request->get('valor')."%")
                                         ->where('tipoPessoa','<>','I')
                                         ->get();
            break;
        }
        return  view('fornecedor.search',compact('fornecedores'))->with('i',(request()->input('page',1) -1) *5);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fornecedor=Fornecedor::find($id);
        return view('fornecedor.edit',compact('fornecedor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'nomeFantasia'=>'required|string|max:255']);
        Fornecedor::find($id)->update($request->all());
        $log = [
            'tipoAcao'=>'Update',
            'objetoAcao'=>'Editou Fornecedor  = '.$request->get('nomeFantasia'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        return redirect()->route('fornecedor.index')->with('success','Fornecedor Atualizado com Sucesso');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fornecedor=Fornecedor::find($id);
        Fornecedor::find($fornecedor->id)->delete();
        $log = [
            'tipoAcao'=>'Delete',
            'objetoAcao'=>'Deletou Fornecedor id = '.$fornecedor->id.' E-mail '.$fornecedor->emailForncedor.' Nome fantasia '.$fornecedor->nomeFantasia,
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        User::where('email',$fornecedor->emailForncedor)
                ->take(1)
                ->delete();
        $log = [
            'tipoAcao'=>'Delete',
            'objetoAcao'=>'Processo delete de fornecedor deletou usuario '.$fornecedor->emailForncedor,
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        $infoinvalidForn = Fornecedor::where('tipoPessoa','I')->take(1)->get();
        Contratos::where('idFornecedor',$fornecedor->id)
                    ->update(['idFornecedor'=>$infoinvalidForn[0]->id]);
        return redirect()->route('fornecedor.index')->with('success','Registro removido com sucesso');

    }
}
