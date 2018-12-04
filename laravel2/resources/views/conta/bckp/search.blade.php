@extends('layouts.app')

@section('content')
    <div class="row">
        <section class="content">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <!-- Mensagens de Validação -->
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

                <!-- Painel de Pesquisa -->
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form action="{{ route('conta.searchFor') }}" method="POST">
                                {{csrf_field()}}

                                <div class="row">
                                    <!-- Filtro: Valor a pesquisar -->
                                    <div class="col-sm-9">
                                        <label>Valor a pesquisar:</label>
                                        <input type="text" name="valor" id="valor" class="form-control"
                                               placeholder="Valor a ser pesquisado">
                                        <input type="hidden" name="id_contrato" id="id_contrato"
                                               class="form-control input-sm" value="{{$contrato[0]->id}}">
                                    </div>

                                    <!-- Filtro: Numero Bancario -->
                                    <div class="col-sm-3">
                                        <label for="type">Pesquisar por:</label>
                                        <select name="type" id="type" class="form-control">
                                            <option value="nb">Numero Bancário</option>
                                        </select>
                                    </div>
                                </div>
                                <br>

                                <!-- Mais opções de pesquisa -->
                                <div class="row">
                                    <div class="collapse" id="camposFiltrosPesquisa">
                                        <!-- Filtro: Status do Extrato -->
                                        <div class="col-sm-3">
                                            <label for="st_ex">Status do Extrato:</label>
                                            <select name="st_ex" id="st_ex" class="form-control">
                                                <option value="%">Todos</option>
                                                <option value="A">Aberto</option>
                                                <option value="AP">Aprovado</option>
                                                <option value="IN">Invalidado</option>
                                            </select>
                                        </div>

                                        <!-- Filtro: Data Inicial -->
                                        <div class="col-sm-3">
                                            <label for="date_in">Data Inicial:</label>
                                            <input class="form-control s"
                                                   id="date_in"
                                                   name="date_in" type="date">
                                        </div>

                                        <!-- Filtro: Data Final -->
                                        <div class="col-sm-3">
                                            <label for="date_fim">Data Final:</label>
                                            <input class="form-control "
                                                   id="date_fim"
                                                   name="date_fim" type="date">
                                        </div>

                                        <!-- Filtro: Data De... -->
                                        <div class="col-sm-3">
                                            <label for="type_date">Tipo Data:</label>
                                            <select name="type_date"
                                                    id="type_date"
                                                    class="form-control ">
                                                <option value="sd">Sem data</option>
                                                <option value="dv">Vencimento</option>
                                                <option value="dc">Criação</option>
                                                <option value="da">Aprovação</option>
                                            </select>
                                        </div>

                                        <!-- Linha de espaçamento transparente -->
                                        <div class="col-sm-12" style="margin:0; padding:0; height:20px;"></div>
                                    </div>

                                    <!-- Botão: Mais opções -->
                                    <div class="col-sm-12">
                                        <button class="btn btn-default"
                                                id="botaoMaisOpcoes"
                                                type="button"
                                                data-toggle="collapse"
                                                data-target="#camposFiltrosPesquisa">

                                            <span class="glyphicon glyphicon-option-horizontal"></span>
                                            Mais opções
                                        </button>
                                    </div>
                                </div>

                                <!-- Linha de espaçamento -->
                                <hr>

                                <!-- Botões de ação do form -->
                                <div class="row">
                                    <div class="col-sm-12">
                                        <!-- Botão: Pesquisar -->
                                        <button class="btn btn-default" style="width: 100px;" type="submit">
                                            <span class="glyphicon glyphicon-search"></span>
                                            Pesquisar
                                        </button>

                                        <!-- Botão: Voltar -->
                                        <a class="btn btn-default" style="width: 100px;"
                                           type="submit" href="{{route('contas.create')}}">
                                            <span class="glyphicon glyphicon-backward"></span>
                                            Voltar
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="alert alert-link">
                                <div class="form-group">
                                    Contrato {{$contrato[0]->descContrato}}  -  {{$contrato[0]->identContratoExt}}<br>
                                    <a style="margin-bottom: 10px" class="btn btn-default"
                                       href="{{ route('contas.create') }}">Trocar de Contrato</a>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <button data-toggle="confirmation_aproved" style="margin-bottom: 10px"
                                        class="btn btn-default aprove_all" data-url="{{ url('contasaproveAll') }}">
                                    Aprovar Todos
                                </button>
                                <button style="margin-bottom: 10px" class="btn btn-group delete_all"
                                        data-url="{{ url('contasdeleteall') }}">Invalidar Todos
                                </button>
                                <a style="margin-bottom: 10px" class="btn btn-info"
                                   href="{{ route('contas.createnew',$contrato[0]->id) }}">Criar Novo</a>
                            </div>
                            <div class="table-container">
                                <table id="mytable" class="table table-bordred table-striped">
                                    <thead>
                                    <th width="50px"><input type="checkbox" id="master"></th>
                                    <th>Status do Extrato</th>
                                    <th>Valor Extrato</th>
                                    <th>Numero Bancário</th>
                                    <th>Vencimento</th>
                                    <th>Documento PDF</th>
                                    <th>Contestação</th>
                                    <th>Ação</th>
                                    <th>Invalidar</th>
                                    </thead>
                                    <tbody>
                                    @if($contas->count())
                                        @foreach($contas as $conta)
                                            <tr id="tr_{{$conta->id}}">
                                                <td>@if($conta->statusConta <> 'A' )@else<input type="checkbox"
                                                                                                class="sub_chk"
                                                                                                data-id="{{$conta->id}}">@endif
                                                </td>
                                                <td>@switch($conta->statusConta)
                                                        @case('A')
                                                        Aberto
                                                        @break
                                                        @case('AP')
                                                        Aprovado
                                                        @break
                                                        @case('IN')
                                                        Invalidado
                                                        @break
                                                        @default
                                                        Sem identificação
                                                @endswitch
                                                <td>R$:{{$conta->valorConta}}</td>
                                                <?php $data = new DateTime($conta->criado_em);?>
                                                <td>{{$conta->NumeroBancario.'-'.$data->format('m-Y')}}</td>
                                                <td><?php $data = new DateTime($conta->vencimentoConta);
                                                    echo $data->format('d-m-Y');?>
                                                </td>
                                                <td>@if($conta->statusConta <> 'AP' )<a class="btn btn-default" target="_blank"
                                                                                        href="{{ URL::to('/pdf/'.$conta->NumeroBancario.'-'.$conta->idContrato.'.pdf') }}">Baixar
                                                        PDF</a>
                                                    @else
                                                        <a class="btn btn-default" target="_blank" href="{{ route('contas.geradpf',['id_conta'=>$conta->id])}}">Baixar PDF</a>
                                                    @endif</td>
                                                <td>@if($conta->idContestacao == 0 ) <a class="btn btn-default"
                                                                                        href="{{route('contas.contasContratoContestaId',['id'=>$conta->id,'id_contrato'=>$conta->idContrato])}}">Criar
                                                        Contestação</a> @else <a class="btn btn-default" href="">Ver
                                                        Contestação</a> @endif</td>
                                                <td>@switch($conta->statusConta)
                                                        @case('A')
                                                        <a class="btn btn-default"
                                                           href="{{ route('contas.aprove',['id'=>$conta->id,'id_contrato'=>$conta->idContrato]) }}">Aprovar</a>
                                                        @break
                                                        @case('AP')
                                                        <a class="btn btn-default"
                                                           href="{{ route('contas.reeopen',['id'=>$conta->id,'id_contrato'=>$conta->idContrato]) }}">Reabrir</a>
                                                        @break
                                                        @case('IN')
                                                        <a class="btn btn-default"
                                                           href="{{ route('contas.reeopen',['id'=>$conta->id,'id_contrato'=>$conta->idContrato]) }}">Reabrir</a>
                                                        @break
                                                        @default
                                                        Sem identificação
                                                    @endswitch </td>
                                                <td>@switch($conta->statusConta)
                                                        @case('A')
                                                        <a class="btn btn-default"
                                                           href="{{ route('contas.invalidate',['id'=>$conta->id,'id_contrato'=>$conta->idContrato]) }}">Invalidar</a>
                                                        @break
                                                        @default

                                                    @endswitch </td>

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
            </div>
        </section>

        <script>
            jQuery(document).ready(function ($) {

                $('.datepicker').datepicker({
                    language: 'pt-BR'

                });
                $('.datepicker2').datepicker({
                    language: 'pt-BR'

                });
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function () {



                //###########seleciona tudo#################

                $('#master').on('click', function (e) {
                    if ($(this).is(':checked', true)) {
                        $(".sub_chk").prop('checked', true);
                    } else {
                        $(".sub_chk").prop('checked', false);
                    }
                });
                //###########Fim Seleciona tudo#################

                //##########Acao Delete All#####################
                $('.delete_all').on('click', function (e) {


                    var allVals = [];
                    $(".sub_chk:checked").each(function () {
                        allVals.push($(this).attr('data-id'));
                    });


                    if (allVals.length <= 0) {
                        alert("Selecione pelo menos uma linha.");
                    } else {


                        var check = confirm("Tem certeza que deseja invalidar essas contas?");
                        if (check == true) {


                            var join_selected_values = allVals.join(",");


                            $.ajax({
                                url: $(this).data('url'),
                                type: 'DELETE',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: 'ids=' + join_selected_values,
                                success: function (data) {
                                    if (data['success']) {
                                        $(".sub_chk:checked").each(function () {
                                            setTimeout(function () {
                                                window.location = window.location
                                            }, 100);
                                        });
                                        alert(data['success']);
                                    } else if (data['error']) {
                                        alert(data['error']);
                                    } else {
                                        alert('Algo Saiu Errado!!');
                                    }
                                },
                                error: function (data) {
                                    alert(data.responseText);
                                }
                            });


                            $.each(allVals, function (index, value) {
                                $('table tr').filter("[data-row-id='" + value + "']").remove();
                            });
                        }
                    }
                });
                $('.aprove_all').on('click', function (e) {


                    var allVals = [];
                    $(".sub_chk:checked").each(function () {
                        allVals.push($(this).attr('data-id'));
                    });


                    if (allVals.length <= 0) {
                        alert("Selecione pelo menos uma linha.");
                    } else {


                        var check = confirm("Tem certeza que deseja aprovar essas contas?");
                        if (check == true) {


                            var join_selected_values = allVals.join(",");


                            $.ajax({
                                url: $(this).data('url'),
                                type: 'POST',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: 'ids=' + join_selected_values,
                                success: function (data) {
                                    if (data['success']) {
                                        $(".sub_chk:checked").each(function () {
                                            setTimeout(function () {
                                                window.location = window.location
                                            }, 100);
                                        });
                                        alert(data['success']);
                                    } else if (data['error']) {
                                        alert(data['error']);
                                    } else {
                                        alert('Algo Saiu Errado!!');
                                    }
                                },
                                error: function (data) {
                                    alert(data.responseText);
                                }
                            });


                            $.each(allVals, function (index, value) {
                                $('table tr').filter("[data-row-id='" + value + "']").remove();
                            });
                        }
                    }
                });

                //###############Liga o data-toggle#################
                $('[data-toggle=confirmation]').confirmation({
                    rootSelector: '[data-toggle=confirmation]',
                    onConfirm: function (event, element) {
                        element.trigger('confirm');
                    }
                });

                $('[data-toggle=confirmation_aproved]').confirmation({
                    rootSelector: '[data-toggle=confirmation_aproved]',
                    onConfirm: function (event, element) {
                        element.trigger('confirm_aproved');
                    }
                });


                $(document).on('confirm', function (e) {
                    var ele = e.target;
                    e.preventDefault();


                    $.ajax({
                        url: ele.href,
                        type: 'DELETE',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function (data) {
                            if (data['success']) {
                                $("#" + data['tr']).slideUp("slow");
                                alert(data['success']);
                                setTimeout(function () {
                                    window.location = window.location
                                }, 100);
                            } else if (data['error']) {
                                alert(data['error']);
                            } else {
                                alert('Algo Saiu Errado!!');
                            }
                        },
                        error: function (data) {
                            alert(data.responseText);
                        }
                    });


                    return false;
                });
                $(document).on('confirm_aproved', function (e) {
                    var ele = e.target;
                    e.preventDefault();


                    $.ajax({
                        url: ele.href,
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function (data) {
                            if (data['success']) {
                                $("#" + data['tr']).slideUp("slow");
                                alert(data['success']);
                                setTimeout(function () {
                                    window.location = window.location
                                }, 100);
                            } else if (data['error']) {
                                alert(data['error']);
                            } else {
                                alert('Algo Saiu Errado!!');
                            }
                        },
                        error: function (data) {
                            alert(data.responseText);
                        }
                    });


                    return false;
                });
            });
        </script>

@endsection
