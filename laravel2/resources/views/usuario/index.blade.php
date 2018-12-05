@extends('layouts.app')

@section('content')
    <div class="row">
        <section class="content">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default table-responsive">
                    <div class="panel-body">
                        @if(Session::has('success'))
                            <div class="alert alert-info">
                                {{Session::get('success')}}
                            </div>
                        @endif
                        <div class="pull-left"><h3>Usuários</h3></div>


                        <div class="pull-right">

                            <div class="btn-group">
                                <a href="{{ route('usuarios.create') }}" class="btn btn-info" >Novo Usuário</a>

                            </div>

                            <div class="btn-group">
                                <a href="{{ route('usuarios.search') }}" class="btn btn-info" >Consulta Usuário</a>

                            </div>
                        </div>
                        <div class="table-container">
                            <table id="mytable" class="table table-bordred table-striped">
                                <thead>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Secretarias</th>
                                <th>Fornecedor</th>
                                <th></th>
                                <th></th>
                                </thead>
                                <tbody>
                                @if($usuarios->count())
                                    @foreach($usuarios as $usuario)
                                        <tr>
                                            <td>{{$usuario->name}}</td>
                                            <td>{{$usuario->email }}</td>
                                            <td>@if($usuario->tipo == 'U')<a class="btn btn-default" href="{{action('SecretariaController@assign', $usuario->id)}}" >Atribuir Secretarias</a>@endif</td>
                                            <td>@if($usuario->tipo == 'F')Sim @else Não @endif</td>
                                            <td><a class="btn btn-primary btn-xs" href="{{action('UsuarioController@edit',$usuario->id)}}" ><span class="glyphicon glyphicon-pencil"></span></a></td>
                                            <td>
                                                <form action="{{route('usuarios.delete')}}" method="post">
                                                    {{csrf_field()}}
                                                    <input name="tipo" type="hidden" value="{{$usuario->tipo}}">
                                                    <input name="idUsuario" type="hidden" value="{{$usuario->id}}">
                                                    <button class="btn btn-danger btn-xs" type="submit"><span class="glyphicon glyphicon-trash"></span></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">Sem Registros !!</td>
                                    </tr>
                                @endif
                                </tbody>

                            </table>
                        </div>
                    </div>
                    {{ $usuarios->links() }}
                </div>
            </div>
        </section>
@endsection