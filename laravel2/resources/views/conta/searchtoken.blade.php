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
                            <form action="{{ route('token.valida') }}" method="get">
                                {{csrf_field()}}

                                <div class="row">
                                    <!-- Filtro: Valor a pesquisar -->
                                    <div class="col-sm-9">
                                        <label>Valor a pesquisar:</label>
                                        <input type="text" name="info" id="info" class="form-control"
                                               placeholder="Digite o token">
                                    </div>

                                </div>
                                <br>

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
                                           type="submit" href="">
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
                    <div class="panel panel-default table-responsive">
                        <div class="panel-body">
                            <div class="table-container">
                                <table id="mytable" class="table table-bordred table-striped">
                                    <thead>
                                    <th>Numero Bancário</th>
                                    <th>Navegador</th>
                                    <th>SO</th>
                                    <th>IP</th>
                                    <th>Token</th>
                                    <th>Aprovador</th>
                                    <th>CPF</th>
                                    <th>Data Aprovação</th>
                                    </thead>
                                    <tbody>
                                    @if($tokens->count())
                                        @foreach($tokens as $token)
                                            <tr id="tr_{{$token->id}}">
                                                <td>{{$token->NumeroBancario}}</td>
                                                <td>{{$token->Navegador}}</td>
                                                <td>{{$token->SO}}</td>
                                                <td>{{$token->IP}}</td>
                                                <td>{{$token->Token}}</td>
                                                <td>{{$token->name}}</td>
                                                <td>{{$token->cpf}}</td>
                                                <td>{{$token->AprovadoEm}}</td>
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
