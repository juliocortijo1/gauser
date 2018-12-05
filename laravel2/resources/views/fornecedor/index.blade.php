@extends('layouts.app')

@section('content')
    <div class="row">
        <section class="content">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default table-responsive">
                    <div class="panel-body">
                        @if(Session::has('success'))
                            <div class="alert alert-info">
                                {{Session::get('success')}}
                            </div>
                        @endif
                        <div class="pull-left"><h3>Fornecedores</h3></div>


                        <div class="pull-right">

                            <div class="btn-group">
                                <a href="{{ route('fornecedor.create') }}" class="btn btn-info" >Novo Fornecedor</a>

                            </div>

                            <div class="btn-group">
                                <a href="{{ route('fornecedor.search') }}" class="btn btn-info" >Consulta Fornecedor</a>

                            </div>
                        </div>
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
                    {{ $fornecedores->links() }}
                </div>
            </div>
        </section>
@endsection