@extends('layouts.app')

@section('content')
    <div class="row">
        <section class="content">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @if(Session::has('success'))
                            <div class="alert alert-info">
                                {{Session::get('success')}}
                            </div>
                        @endif
                        <div class="pull-left"><h3>Contratos</h3></div>


                        <div class="pull-right">

                            <div class="btn-group">
                                <a href="{{  route('contratos.create') }}" class="btn btn-info" >Novo Contrato</a>

                            </div>

                            <div class="btn-group">
                                <a href="{{  route('contratos.search') }}" class="btn btn-info" >Consulta Contratos</a>

                            </div>
                        </div>
                        <div class="table-container">
                            <table id="mytable" class="table table-bordred table-striped">
                                <thead align="center">
                                <th>Fornecedor</th>
                                <th>Status do Acontrato</th>
                                <th>Descrição</th>
                                <th>Identificação Externa</th>
                                <th>Secretaria</th>
                                <th>Editar</th>
                                <th>Remover</th>
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
                                            <td><a class="btn btn-primary btn-xs" href="{{action('ContratosController@edit', $contrato->id)}}" ><span class="glyphicon glyphicon-pencil"></span></a></td>
                                            <td>
                                                <form action="{{route('contratos.delete')}}" method="post" >
                                                    {{csrf_field()}}
                                                    <input name="idContrato" id="idContrato" type="hidden" value="{{$contrato->id}}">
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
                    {{ $contratos->links() }}
                </div>
            </div>
        </section>
@endsection