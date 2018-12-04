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
                        <h3 class="panel-title">Novo Fornecedor</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-container">
                            <form method="POST" action="{{ route('fornecedor.update',$fornecedor->id) }}"   role="form">
                                {{ csrf_field() }}
                                <input name="_method" type="hidden" value="PATCH">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="nomeFantasia" id="nomeFantasia" class="form-control input-sm" value="{{$fornecedor->nomeFantasia}}">
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="telFornecedor" id="telFornecedor" class="form-control input-sm" value="{{$fornecedor->telFornecedor}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="emailForncedor" id="emailForncedor" class="form-control input-sm" value="{{$fornecedor->emailForncedor}}" disabled>
                                        </div>
                                    </div>


                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                        <div class="form-group">,
                                            <label>Tipo Pessoa</label>
                                            <select name="tipoPessoa" id="tipoPessoa" class=" input-sm">
                                                <option value="F" @if($fornecedor->tipoPessoa == 'F')selected @endif >Física</option>
                                                <option value="J" @if($fornecedor->tipoPessoa == 'J')selected @endif>Jurídica</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <input type="submit"  value="Salvar" class="btn btn-info">
                                        <a href="{{ route('fornecedor.index') }}" class="btn btn-default " >Voltar</a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </section>
@endsection