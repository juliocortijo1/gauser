<?php

namespace App\Http\Controllers;
use App\Fornecedor;
use App\User;
use App\Auth;
use Illuminate\Http\Request;
use App\Log;
class UsuarioController extends Controller
{

    public function index()
    {
        $usuarios = User::latest()->paginate(5);
        return view('usuario.index', compact('usuarios'))->with('i',(request()->input('page',1) -1) *5);

    }

    public function search()
    {
        $usuarios = User::latest()->paginate(5);
        return view('usuario.search', compact('usuarios'))->with('i',(request()->input('page',1) -1) *5);

    }

    public function create(){
        return  view('usuario.register');
    }

    public function SefEdit(){
        $usuario=User::find(\auth()->user()->id);
        return  view('usuario.edituser',compact('usuario'));
    }

    public function edit($id){
    $usuario=User::find($id);
    return  view('usuario.edit',compact('usuario'));
    }



    public function UpdateMe(Request $request){


        $this->validate($request,[
            'name'=>'required|string|max:255']);

        if($request->get('password')){
        $infouser= array(
            'name' => $request->get('name'),
            'password' => bcrypt($request->get('password'))

        );
        }else{
            $infouser= array(
                'name' => $request->get('name'),
            );
        }
        User::find(\auth()->user()->id)->update($infouser);
        $log = [
            'tipoAcao'=>'Update',
            'objetoAcao'=>'Usuario atualizou suas informações = '.\auth()->user()->id,
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        return redirect()->route('usuarios.me')->with('success','Usuario  editado com sucesso');
    }

    public function delete(Request $request)
    {
        $id = $request->get('idUsuario');
        $infouser=User::find($id);
        $log = [
            'tipoAcao'=>'Delete',
            'objetoAcao'=>'Removeu usuario = '.$infouser->email,
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        if($infouser->tipo == 'F'){
           /* Fornecedor::where('emailForncedor',$infouser->email)
                ->take(1)
                ->delete();
           */
            User::find($infouser->id)->delete();
            return redirect()->route('usuarios.index')->with('success','Usuário removido com sucesso');
        }else{
            $infouser=User::find($infouser->id)->delete();
            return redirect()->route('usuarios.index')->with('success','Usuário removido com sucesso');
        }
    }

    public function searchFor(Request $request)
    {
        switch($request->get('type'))
        {
            case 'n':
                $usuarios = User::where('name','LIKE',"%".$request->get('valor')."%")
                    ->get();
                break;
            case 'e':
                $usuarios = User::where('email','LIKE',"%".$request->get('valor')."%")
                    ->get();
                break;
        }
        return  view('usuario.search',compact('usuarios'))->with('i',(request()->input('page',1) -1) *5);
    }
    public function Update(Request $request){


        $this->validate($request,[
            'name'=>'required|string|max:255']);

        if($request->get('password')){
            $infouser= array(
                'name' => $request->get('name'),
                'password' => bcrypt($request->get('password')),
                'tipo' =>'U',
                'permissao'=>$request->get('perm')

            );
        }else{
            $infouser= array(
                'name' => $request->get('name'),
                'permissao'=>$request->get('perm')
            );
        }
        User::find($request->get('idUsuario'))->update($infouser);
        $log = [
            'tipoAcao'=>'Update',
            'objetoAcao'=>'Atualizou usuario = '. $request->get('name').' Id '. $request->get('idUsuario'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        return redirect()->route('usuarios.index')->with('success','Usuario  editado com sucesso');
    }



    public function CreateNew(Request $request){


        $this->validate($request,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed']);


            $infouser= array(
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->get('password')),
                'permissao'=>$request->get('perm'),
                'tipo' =>'U',

            );
        User::create($infouser);
        $log = [
            'tipoAcao'=>'Create',
            'objetoAcao'=>'Criou usuario = '. $request->get('name').' Email '. $request->get('email'),
            'idUsuarioAcao' => \auth()->user()->id,
            'dataAcao'=>date('Y-m-d H:i'),
        ];

        Log::create($log);
        return redirect()->route('usuarios.index')->with('success','Usuario  editado com sucesso');
    }
}
