@extends('layouts.app')
@section('content')
    <script>
        jQuery(document).ready(function ($) {
            $("#cpf").mask("999.999.999-99");
        });
    </script>
    <div class="row">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <!-- Titulo Painel -->
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Assinatura de Arquivos
                        </h3>
                    </div>

                    <!-- Corpo do painel -->
                    <div class="panel-body">
                        <!-- Mensagens de Validação -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            @if(Session::has('success'))
                                <div class="alert alert-info">
                                    {{Session::get('success')}}
                                </div>
                            @endif
                        @endif

                        <form action="{{ route('usuarios.assinaturaEnviar') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <!-- Imagem -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Imagem
                                </div>
                                <div class="panel-body">

                                    <label class="btn btn-primary">
                                        <span class="glyphicon glyphicon-folder-open"></span>
                                        Escolher arquivo...
                                        <input type="file"
                                               name="arquivo_assinatura"
                                               style="display: none !important;">
                                    </label>

                                </div>
                            </div>
                                <div class="panel panel-default align table-responsive">
                                @if($assinatura->count())
                                    <img  height="251" src="{{URL::to('/').$assinatura[0]->assinatura}}" class="img-responsives" align="center"/>
                            @endif
                                </div>
                            <!-- CPF -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    CPF
                                </div>
                                <div class="panel-body">
                                    <div class="form-group-sm">
                                        <label for="cpf">
                                            Número do CPF:
                                        </label>
                                        @if($assinatura->count())
                                        <input type="text" class="form-control" id="cpf" name="cpf" value="{{$assinatura[0]->cpf}}">
                                         @else
                                            <input type="text" class="form-control" id="cpf" name="cpf" value="">
                                          @endif
                                    </div>
                                </div>

                            </div>

                            <!-- Botão: Salvar -->
                            <button type="submit" class="btn btn-default">
                                <span class="glyphicon glyphicon-floppy-disk"></span>
                                Salvar
                            </button>
                                <a href="{{ URL::previous() }}" class="btn btn-default" type="submit"><span class="glyphicon glyphicon-backward"> Voltar</span></a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

