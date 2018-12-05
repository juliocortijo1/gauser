@extends('layouts.app')

@section('content')
    <div class="row">
        <section class="content">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                      <div class="form-group">
                        <form action="{{ route('secretaria.searchForassign') }}" method="POST">

                        {{csrf_field()}}

                             <div class="row">

                            <div class="col-md-8 ">
                                <label>Valor a pesquisar</label>
                                <div class="form-group">
                                    <input type="text" name="valor" id="valor" class="form-control input-sm" placeholder="Valor a ser pesquisado">
                                    <input type="hidden" name="idUsuario" id="idUsuario" value="{{$id}}">
                                </div>

                            </div>
                            <div class="form-group">
                                <label>Pesquisar por</label>
                                <div class="form-group">,

                                    <select name="type" id="type" class=" input-sm">
                                        <option value="n">Nome</option>
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

                <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default table-responsive">
                    <div class="panel-body">
                        @if(Session::has('success'))
                            <div class="alert alert-info">
                                {{Session::get('success')}}
                            </div>
                        @endif
                        @if(Session::has('error'))
                                <div class="alert alert-warning">
                                    {{Session::get('error')}}
                                </div>
                        @endif
                        <div class="pull-left"><h3>Secretarias</h3>
                        </div>

                        <div class="table-container">
                            <table id="mytable" class="table table-bordred table-striped">
                                <thead>
                                <th>Secretaria</th>
                                <th>Associar</th>
                                </thead>
                                <tbody>
                                @if($secretarias->count())
                                    @foreach($secretarias as $secretaria)
                                        <tr>
                                            <td>{{$secretaria->descSecretaria}}</td>
                                            <td>
                                                <form action="{{route('secretaria.assignSecUser')}}" method="post">
                                                    {{csrf_field()}}
                                                    <input name="idUsuario" type="hidden" value="{{$id}}">
                                                    <input name="idSecretaria" type="hidden" value="{{$secretaria->id}}">
                                                    <button class="btn btn-default" type="submit">Associar Secretaria</button>
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

    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-body">

                <div class="pull-left"><h3>Secretarias Associadas</h3>
                </div>


                <div class="table-container">
                    <table id="mytable" class="table table-bordred table-striped">
                        <thead>
                        <th>Secretaria</th>
                        <th>Ação</th>
                        </thead>
                        <tbody>
                        @if($assigns->count())
                            @foreach($assigns as $assign)
                                <tr>
                                    <td>{{$assign->descSecretaria}}</td>
                                    <td>
                                        <form action="{{route('secretaria.DeassignSecUser')}}" method="post">
                                            {{csrf_field()}}
                                            <input name="idUsuario" type="hidden" value="{{$id}}">
                                            <input name="idSecretaria" type="hidden" value="{{$assign->idSecretaria}}">
                                            <button class="btn btn-default" type="submit">Desassociar Secretaria</button>
                                        </form>
                                    </td>

                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8">Sem secretarias associadas ao usuário</td>
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