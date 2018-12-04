<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Titulo -->
    <title>Sistema GDC</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

</head>
<body>
<div id="app">
    <!-- Menu -->
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container-fluid">
            <!-- Logo e Menu de Navegação Mobile-->
            <div class="navbar-header">
                <!-- Botão Navegação Mobile -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Navegação Mobile</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Logo ou Nome da Aplicação -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    Gerenciador de Contas
                </a>
            </div>

            <!-- Lista de Links de Acesso -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <!-- Links para usuários logados -->
                    @guest
                        <li><a href="{{ route('login') }}">Logar</a></li>
                    @else
                    <!-- Opção Menu: Contas -->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false" aria-haspopup="true" v-pre>
                                Contas
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('contas.create') }}">Cadastrar</a></li>
                                <li><a href="{{ route( 'contas.contasContratoall','A') }}">Abertas</a></li>
                                <li><a href="{{ route( 'contas.contasContratoall','AP') }}">Aprovadas</a></li>
                                <li><a href="{{ route( 'contas.contasContratoall','IN') }}">Invalidadas</a></li>
                            </ul>
                        </li>

                        <!-- Opção Menu c/ Acesso Restrito -->
                        @if ((Auth::user()->permissao == 1) && (Auth::user()->tipo == 'U') )
                            <li><a href="{{ route('fornecedor.index') }}">Fornecedores</a></li>
                            <li><a href="{{ route('contratos.index') }}">Contratos</a></li>
                            <li><a href="{{ route('secretaria.index') }}">Secretarias</a></li>
                            <li><a href="{{ route('usuarios.index') }}">Usuários</a></li>
                        @endif

                    <!-- Opções Menu: Usuário-->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false" aria-haspopup="true" v-pre>
                                {{ Auth::user()->name }}
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('usuarios.me') }}">Alterar Senha</a></li>
                                <li><a href="{{ route('usuarios.assinatura') }}">Alterar Assinatura</a></li>

                                <li>
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        Sair
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>
