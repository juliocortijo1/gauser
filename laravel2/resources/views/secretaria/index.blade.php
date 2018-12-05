@extends('layouts.app')

@section('content')
    <div class="row">
        <section class="content">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default table-responsive"  >
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
                        <div class="pull-left"><h3>Secretarias</h3></div>


                        <div class="pull-right">

                            <div class="btn-group">
                                <a href="{{ route('secretaria.create') }}" class="btn btn-info" >Nova Secretaria</a>

                            </div>

                            <div class="btn-group">
                                <a href="{{ route('secretaria.search') }}" class="btn btn-info" >Consulta Secretaria</a>

                            </div>
                        </div>
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
                    {{ $secretarias->links() }}
                </div>
            </div>
        </section>
@endsection