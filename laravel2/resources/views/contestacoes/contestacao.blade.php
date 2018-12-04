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
                        <h3 class="panel-title">Novo Extrato</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-container">



                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                        <div class="form-group" >
                                            <label>Valor do Extrato R$:</label>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon">R$</span>
                                            <input type="text"  min="0" step="0.01" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency" value="{{$conta->valorConta}}"  disabled />
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                        <div class="form-group" >
                                            <label>Número Bancário:</label>
                                        </div>
                                        <div class="form-group">
                                            <input type="number" name="NumeroBancario" id="NumeroBancario" class="form-control input-sm" value="{{$conta->NumeroBancario}}"  disabled>
                                        </div>

                                    </div>

                                </div>


                                <div class="col-xs-6 col-sm-6 col-md-6">

                                    <div class="form-group" >
                                        <label>Contrato Associado</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="text"  class="form-control input-sm" value="{{$contratos->descContrato}} | {{$contratos->identContratoExt}} " disabled>
                                    </div>
                                    <div class="form-group" >
                                        <label>Email do Fornecedor</label>
                                        <h5>Para mais destinatarios use vírgulas para separar os e-mails</h5>
                                    </div>




                            <form method="POST" action="{{route('contas.contestacaoEnviar')}}"  role="form">
                                {{ csrf_field() }}
                                <div class="form-group">
                                <input type="mail" name="email" id="email"  value="{{$fornecedor->emailForncedor}}"  class="form-control input-sm" >
                                </div>
                                <input type="hidden"  name="valorConta" id="valorConta" value="{{$conta->valorConta}}" >
                                <input type="hidden" name="NumeroBancario" id="NumeroBancario"  value="{{$conta->NumeroBancario}}" >
                                <input type="hidden" name="DescContrato" id="DescContrato"  value="{{$contratos->descContrato}} | {{$contratos->identContratoExt}} ">

                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <textarea name="descricao" rows="10" cols="80" placeholder="Escreva uma descrição para o problema...."></textarea>
                                    </div>

                                    <input type="submit"  value="Enviar Contestação" class="btn btn-info">
                                    <a href="{{ url()->previous() }}"  class="btn btn-default" >Voltar</a>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </section>


@endsection