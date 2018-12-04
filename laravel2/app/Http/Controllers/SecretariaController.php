<?php

namespace App\Http\Controllers;

use App\Contratos;
use App\Secretaria;
use App\AssignSecretaria;
use App\Log;
use Illuminate\Http\Request;

class SecretariaController extends Controller
{
    public function index()
    {
        $secretarias = Secretaria::where('descSecretaria','<>','Default')
                       ->latest()->paginate(5);
        return view('secretaria.index', compact('secretarias'))->with('i',(request()->input('page',1) -1) *5);

    }
    public function search()
    {
        $secretarias = Secretaria::where('descSecretaria','<>','Default')
            ->latest()->paginate(5);
        return view('secretaria.search', compact('secretarias'))->with('i',(request()->input('page',1) -1) *5);

    }
    public function delete(Request $request)
    {

        $log = [
            'tipoAcao'=>'Delete',
            'objetoAcao'=>'Deletou secretaria id = '.$request->get('id'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);

        if($request->get('id') == 1){
            return redirect()->route('secretaria.index')->with('error','Você não pode remover a Administração');
        }else{
            $secretarias=Secretaria::find($request->get('id'))->delete();
            $secretaria_default_id = Secretaria::where('descSecretaria','Default')->take(1)->get();
            Contratos::where('idSecretaria',$request->get('id'))
                       ->update(['idSecretaria'=>$secretaria_default_id[0]->id]);
            return redirect()->route('secretaria.index')->with('success','Secretaria removida com sucesso');
        }
    }


    public function searchFor(Request $request)
    {
        $secretarias = Secretaria::where('descSecretaria','LIKE',"%".$request->get('valor')."%")
                    ->where('descSecretaria','<>','Default')
                    ->get();
        return  view('secretaria.search',compact('secretarias'))->with('i',(request()->input('page',1) -1) *5);
    }

    public function searchForassign(Request $request)
    {
        $secretarias = Secretaria::where('descSecretaria','LIKE',"%".$request->get('valor')."%")
            ->where('descSecretaria','<>','Default')
            ->get();
        $assigns = AssignSecretaria::join('tblSecretaria', 'tblSecretariaParaUsuario.idSecretaria', '=', 'tblSecretaria.id')
            ->where('idUsuario','=',$request->get('idUsuario'))
            ->select('tblSecretariaParaUsuario.id','tblSecretaria.descSecretaria','tblSecretariaParaUsuario.idSecretaria','tblSecretariaParaUsuario.idUsuario')
            ->paginate(5);

        return  view('secretaria.searchsecretaria',compact('secretarias','assigns'))->with('i',(request()->input('page',1) -1) *5)
                                                                                                      ->with('id',$request->get('idUsuario'));
    }

    public function edit($id){
        $secretarias=Secretaria::find($id);
        return  view('secretaria.edit',compact('secretarias'));
    }

    public function assign($id){
        $secretarias=Secretaria::where('descSecretaria','<>','Default')->get();
        $assigns = AssignSecretaria::join('tblSecretaria', 'tblSecretariaParaUsuario.idSecretaria', '=', 'tblSecretaria.id')
                                    ->where('idUsuario','=',$id)
                                    ->select('tblSecretariaParaUsuario.id','tblSecretaria.descSecretaria','tblSecretariaParaUsuario.idSecretaria','tblSecretariaParaUsuario.idUsuario')
                                    ->paginate(5);

        return  view('secretaria.searchsecretaria',compact('secretarias','assigns'))->with('i',(request()->input('page',1) -1) *5)
                                                                                                      ->with('id',$id);
    }
    public function assignSecUser(Request $request)
    {
        $total = AssignSecretaria::where('idUsuario', '=', $request->get('idUsuario'))
            ->where('idSecretaria', '=', $request->get('idSecretaria'))
            ->count();
        if ($total > 0) {
                return redirect()->route('secretaria.assign',['id'=>$request->get('idUsuario')])->with('i', (request()->input('page', 1) - 1) * 5)
                ->with('error', 'Secretaria já associada ao usuário');

        } else {

         AssignSecretaria::create($request->all());
            $log = [
                'tipoAcao'=>'Update',
                'objetoAcao'=>'Associou secretaria id = '.$request->get('idSecretaria').' Para Usuario '.$request->get('idUsuario'),
                'idUsuarioAcao' => \auth()->user()->id,
                'dataAcao'=>date('Y-m-d H:i'),
            ];

            Log::create($log);


            return redirect()->route('secretaria.assign',['id'=>$request->get('idUsuario')])->with('i', (request()->input('page', 1) - 1) * 5)
                ->with('success', 'Secretaria associada com sucesso');
        }
    }
    public function DeassignSecUser(Request $request)
    {
        $teste=AssignSecretaria::where('idSecretaria',$request->get('idSecretaria'))
                        ->where('idUsuario',$request->get('idUsuario'))
                        ->take(1)
                        ->delete();
        $log = [
            'tipoAcao'=>'Update',
            'objetoAcao'=>'Desassociou secretaria id = '.$request->get('idSecretaria').' Para Usuario '.$request->get('idUsuario'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        return redirect()->route('secretaria.assign',['id'=>$request->get('idUsuario')])->with('i', (request()->input('page', 1) - 1) * 5)
            ->with('success', 'Secretaria desassociada com Sucesso');
    }
    public function create()
    {
        return view('secretaria.create');
    }

    public function update(Request $request)
    {
        $this->validate($request,[
            'descSecretaria'=>'required|string|max:255']);
        if($request->get('id')==1) {
            return redirect()->route('secretaria.index')->with('error', 'Administração não pode ser atualizada');
        }else{
            Secretaria::find($request->get('id'))->update($request->all());
            $log = [
                'tipoAcao'=>'Update',
                'objetoAcao'=>'Atualizou secretaria id = '.$request->get('idSecretaria'),
                'idUsuarioAcao' => \auth()->user()->id,
                'dataAcao'=>date('Y-m-d H:i'),
            ];

            Log::create($log);
            return redirect()->route('secretaria.index')->with('success', 'Secretaria Atualizada com Sucesso');
        }
    }
    public function store(Request $request)
    {
        $this->validate($request,[
            'descSecretaria'=>'required|string|max:200']);
        Secretaria::create($request->all());
        $log = [
            'tipoAcao'=>'Create',
            'objetoAcao'=>'Criou secretaria '.$request->get('descSecretaria'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        return redirect()->route('secretaria.index')->with('success','Secretaria cadastrada com sucesso');
    }


}

