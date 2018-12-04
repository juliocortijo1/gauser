@extends('layouts.app')

@section('content')
    <script>
        jQuery(document).ready(function ($) {
            $('#mytable').DataTable({
                    "responsive": true,
                    "oLanguage": {
                        "sProcessing": "Processando...",
                        "sLengthMenu": "Mostrar _MENU_ registros",
                        "sZeroRecords": "Não foram encontrados resultados",
                        "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando de 0 até 0 de 0 registros",
                        "sInfoFiltered": "",
                        "sInfoPostFix": "",
                        "sSearch": "Busca Geral:",
                        "sUrl": "",
                        "oPaginate": {
                            "sFirst": "Primeiro",
                            "sPrevious": "Anterior",
                            "sNext": "Seguinte",
                            "sLast": "Último"
                        }
                    }
                }
            )
        });
    </script>
    <div class="row">
        <section class="content">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                    <div class="form-group">
                        <form action="{{ route('fornecedor.searchFor') }}" method="POST">

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
                                <a href="{{route('fornecedor.index')}}" class="btn btn-default" type="submit"><span class="glyphicon glyphicon-backward"> Voltar</span></a>
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
                        <div class="pull-left"><h3>Fornecedores</h3></div>


                        <div class="table-container">
                            <table id="mytable" class="table table-bordred table-striped">
                                <thead>
                                <th>Nome</th>
                                <th>Tipo Pessoa</th>
                                <th>Telefone</th>
                                <th>E-mail</th>
                                <th>Editar</th>
                                <th>Remover</th>
                                </thead>
                                <tbody>
                                @if($fornecedores->count())
                                    @foreach($fornecedores as $fornecedor)
                                        <tr>
                                            <td>{{$fornecedor->nomeFantasia}}</td>
                                            <td>@if($fornecedor->tipoPessoa == 'J') Jurídica @else Física @endif</td>
                                            <td>{{$fornecedor->telFornecedor}}</td>
                                            <td>{{$fornecedor->emailForncedor}}</td>
                                            <td><a class="btn btn-primary btn-xs" href="{{action('FornecedorController@edit', $fornecedor->id)}}" ><span class="glyphicon glyphicon-pencil"></span></a></td>
                                            <td>
                                                <form action="{{action('FornecedorController@destroy', $fornecedor->id)}}" method="post">
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