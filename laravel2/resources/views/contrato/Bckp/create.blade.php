@extends('layouts.app')

@section('content')
    <div class="row">
        <section class="content">
            <div class="col-md-8 col-md-offset-2">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Error!</strong> Verifique os campos Obrigatórios.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(Session::has('success'))
                    <div class="alert alert-info">
                        {{Session::get('success')}}
                    </div>
                @endif

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Novo Contrato</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-container">

                            <form method="POST" action="{{action('ContratosController@store')}}"   role="form">
                                {{ csrf_field() }}
                                <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                            <div class="form-group" >
                                                <label>Descrição do Contrato</label>
                                            </div>
                                            <div class="form-group">
                                            <input type="text" name="descContrato" id="descContrato" class="form-control input-sm" placeholder="Descrição do Contrato">
                                            </div>
                                        </div>
                                         <div class="col-xs-6 col-sm-6 col-md-6">
                                             <div class="form-group" >
                                                 <label>Identificação Externa do Contrato</label>
                                             </div>
                                            <div class="form-group">
                                            <input type="text" name="identContratoExt" id="identContratoExt" class="form-control input-sm" placeholder="Identificação do Contrato via Sistema Externo">
                                            </div>
                                        </div>
                                </div>

                                <div class="col-xs-4 col-sm-4 col-md-4">
                                         <div class="form-group" >
                                                <label>Status do Contrato</label>
                                         </div>

                                            <select name="statusContrato" id="statusContrato" class=" input-sm">
                                                <option value="A" selected>Ativo</option>
                                                <option value="I" >Inativo</option>
                                            </select>
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4">
                                    <div class="form-group" >
                                        <label>Secretaria</label>
                                    </div>
                                        <select name="idSecretaria" id="idSecretaria" class=" input-sm" >
                                            @foreach($secretarias as $secretaria)
                                                <option value="{{$secretaria->id}}" selected>{{$secretaria->descSecretaria}}</option>
                                            @endforeach
                                        </select>

                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4">

                                    <div class="form-group" >
                                        <label>Fornecedor</label>
                                    </div>
                                    <div class="form-group" >
                                        <select name="idFornecedor" id="idFornecedor" class=" input-sm" >
                                            @foreach($fornecedores as $fornecedor)
                                                <option value="{{$fornecedor->id}}" selected>{{$fornecedor->nomeFantasia}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <input type="submit"  value="Salvar" class="btn btn-info">
                                        <a href="{{ route('contratos.index') }}"  class="btn btn-default" >Voltar</a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </section>
@endsection