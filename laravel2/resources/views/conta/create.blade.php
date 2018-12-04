@extends('layouts.app')

@section('content')
    <script>
        jQuery(document).ready(function ($) {
            $("#valorConta").mask("#.##0,00" , { reverse:true});
        });
    </script>
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

                            <form method="POST" action="{{route('contas.store')}}" enctype="multipart/form-data"  role="form">
                                {{ csrf_field() }}
                                <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                            <div class="form-group" >
                                                <label>Valor do Extrato R$:</label>
                                            </div>
                                            <div class="input-group">
                                                <span class="input-group-addon">R$</span>
                                                <input type="text" value=""  class="form-control valorConta" name="valorConta" id="valorConta"  />

                                            </div>
                                        </div>
                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                        <div class="form-group" >
                                            <label>Número Bancário:</label>
                                        </div>
                                        <div class="form-group">
                                            <input type="number" name="NumeroBancario" id="NumeroBancario" class="form-control input-sm" placeholder="Número Bancário">
                                        </div>
                                    </div>
                                         <div class="col-xs-6 col-sm-6 col-md-6">
                                             <div class="form-group" >
                                                 <label>Vencimento do Extrato</label>
                                             </div>
                                            <div class="form-group">
                                                {{Form::date('vencimentoConta', \Carbon\Carbon::now(),array('class' => 'form-control input-sm'))}}

                                            </div>
                                        </div>
                                </div>

                                <div class="col-xs-6 col-sm-6 col-md-6">
                                         <div class="form-group" >
                                                <label>PDF do Extrato</label>
                                         </div>
                                    <input type="file" name="extratopdf" value="">

                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">

                                        <div class="form-group" >
                                            <label>Contrato Associado</label>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="DescContrato" id="DescContrato" class="form-control input-sm" value="{{$contratos->descContrato}} | {{$contratos->identContratoExt}} " disabled>
                                            <input type="hidden" name="idContrato" id="idContrato" class="form-control input-sm" value="{{$contratos->id}}">
                                        </div>




                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <input type="submit"  value="Salvar" class="btn btn-info">
                                        <a href="{{ route('contas.create') }}"  class="btn btn-default" >Voltar</a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </section>


@endsection