@extends('layouts.app')

@section('content')
    <div class="row">
        <section class="content">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default table-responsive">
                    <div class="panel-body">
                    <div class="form-group">
                        <form action="{{ route('usuarios.searchFor') }}" method="POST">

                        {{csrf_field()}}

                        <div class="row">

                            <div class="col-md-8 ">
                                <label>Valor a pesquisar</label>
                                <div class="form-group">
                                    <input type="text" name="valor" id="valor" class="form-control input-sm" placeholder="Valor a ser pesquisado">
                                </div>

                            </div>
                            <div class="form-group">
                                <label>Pesquisar por</label>
                                <div class="form-group">,

                                    <select name="type" id="type" class=" input-sm">
                                        <option value="n">Nome</option>
                                        <option value="e">E-mail</option>
                                    </select>
                                </div>

                            </div>

                        </div>
                            <div class="form-group">
                                <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"> Pesquisar</span></button>
                                <a href="{{route('usuarios.index')}}" class="btn btn-default" type="submit"><span class="glyphicon glyphicon-backward"> Voltar</span></a>
                            </div>
                            </div>
                     </div>

                    </form>
                </div>
            </div>
            </div>
                <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @if(Session::has('success'))
                            <div class="alert alert-info">
                                {{Session::get('success')}}
                            </div>
                        @endif
                        <div class="pull-left"><h3>Usuários</h3></div>


                            <div class="table-container">
                                <table id="mytable" class="table table-bordred table-striped">
                                    <thead>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    </thead>
                                    <tbody>
                                    @if($usuarios->count())
                                        @foreach($usuarios as $usuario)
                                            <tr>
                                                <td>{{$usuario->name}}</td>
                                                <td>{{$usuario->email }}</td>
                                                <td><a class="btn btn-primary btn-xs" href="{{action('UsuarioController@edit', $usuario->id)}}" ><span class="glyphicon glyphicon-pencil"></span></a></td>
                                                <td>
                                                    <form action="" method="post">
                                                        {{csrf_field()}}
                                                        <input name="_method" type="hidden" value="DELETE">

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

                </div>
            </div>
        </section>
@endsection