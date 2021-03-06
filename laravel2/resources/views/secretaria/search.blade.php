@extends('layouts.app')

@section('content')
    <div class="row">
        <section class="content">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                    <div class="form-group">
                        <form action="{{ route('secretaria.searchFor') }}" method="POST">

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
                                    </select>
                                </div>

                            </div>

                        </div>
                            <div class="form-group">
                                <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"> Pesquisar</span></button>
                                <a href="{{route('secretaria.index')}}" class="btn btn-default" type="submit"><span class="glyphicon glyphicon-backward"> Voltar</span></a>
                            </div>
                            </div>
                     </div>

                    </form>
                </div>
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
                        <div class="pull-left"><h3>Fornecedores</h3></div>


                        <div class="table-container">
                            <table id="mytable" class="table table-bordred table-striped">
                                <thead>
                                <th>Secretaria</th>
                                <th>Editar</th>
                                <th>Remover</th>
                                </thead>
                                <tbody>
                                @if($secretarias->count())
                                    @foreach($secretarias as $secretaria)
                                        <tr>
                                            <td>{{$secretaria->descSecretaria}}</td>
                                            <td>@if($secretaria->id == 1)@else<a class="btn btn-primary btn-xs" href="{{action('SecretariaController@edit', $secretaria->id)}}" ><span class="glyphicon glyphicon-pencil"></span></a>@endif</td>
                                            <td>@if($secretaria->id == 1)@else
                                                    <form action="{{route('secretaria.delete')}}" method="post">
                                                        {{csrf_field()}}
                                                        <input name="id" type="hidden" value="{{$secretaria->id}}">

                                                        <button class="btn btn-danger btn-xs" type="submit"><span class="glyphicon glyphicon-trash"></span></button>
                                                    </form>
                                                @endif
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