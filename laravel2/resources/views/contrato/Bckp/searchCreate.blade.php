@extends('layouts.app')

@section('content')
    <div class="row">
        <section class="content">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <form action="{{ route('contratos.searchForToExtrato') }}" method="POST">

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
                                                    <option value="f">Fornecedor</option>
                                                    <option value="s">Secretaria</option>
                                                    <option value="d">Descrição</option>
                                                    <option value="ie">Identificação Externa</option>
                                                </select>
                                            </div>

                                    </div>

                                </div>
                                    <div class="form-group">
                                        <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"> Pesquisar</span></button>

                                    </div>
                            </form>
                        </div>
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
                            @if(Session::has('error'))
                                <div class="alert alert-warning">
                                    {{Session::get('error')}}
                                </div>
                            @endif
                        <div class="pull-left"><h3>Selecione um Contrato para o Extrato</h3></div>

                        <div class="table-container">
                            <table id="mytable" class="table table-bordred table-striped">
                                <thead align="center">
                                <th>Fornecedor</th>
                                <th>Status do Contrato</th>
                                <th>Descrição</th>
                                <th>Identificação Externa</th>
                                <th>Secretaria</th>
                                <th>Novo  Extrato</th>
                                <th>Ver  Extratos</th>
                                </thead>
                                <tbody>
                                @if($contratos->count())
                                    @foreach($contratos as $contrato)
                                        <tr>
                                            <td>{{$contrato->nomeFantasia}}</td>
                                            <td>@if($contrato->statusContrato == 'A') Ativo @else Inativo @endif</td>
                                            <td>{{$contrato->descContrato}}</td>
                                            <td>{{$contrato->identContratoExt}}</td>
                                            <td>{{$contrato->descSecretaria}}</td>
                                            <td><a class="btn btn-default" href=" {{URL::to('contas/'.$contrato->id.'/createnew')}}" >Associar  Extrato</a></td>
                                            <td><a class="btn btn-default" href=" {{URL::to('contas/'.$contrato->id.'/contasContratoId')}}" >Extrato</a></td>
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