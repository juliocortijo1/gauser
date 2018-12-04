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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">






    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.0/jquery.mask.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>




    <![endif]-->
    <link rel="stylesheet" href="https://formden.com/static/cdn/bootstrap-iso.css"/>
    <!-- Bootstrap Date-Picker Plugin -->
    <script type="text/javascript"
            src="https://uxsolutions.github.io/bootstrap-datepicker/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript"
            src="https://uxsolutions.github.io/bootstrap-datepicker/bootstrap-datepicker/js/locales/bootstrap-datepicker.pt-BR.min.js"></script>
    <link type="text/css" rel="stylesheet"
          href="https://uxsolutions.github.io/bootstrap-datepicker/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
    <!-- Styles -->
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">



    <script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">

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
                                <li><a href="{{ route( 'token.valida') }}">Token</a></li>
                            </ul>
                        </li>

                        <!-- Opção Menu c/ Acesso Restrito -->

                        @if ((Auth::user()->permissao == 1) && (Auth::user()->tipo == 'U') )
                            <li><a href="{{ route('secretaria.index') }}">Secretarias</a></li>
                            <li><a href="{{ route('usuarios.index') }}">Usuários</a></li>
                        @endif
                        <li><a href="{{ route('contratos.index') }}">Contratos</a></li>
                        <li><a href="{{ route('fornecedor.index') }}">Fornecedores</a></li>

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

	<!-- Conteudo -->
    @yield('content')
	
	<!-- Scripts -->
	<script src="{{ asset('/js/app.js') }}"></script>
	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>


	
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

</div>
</body>
</html>
