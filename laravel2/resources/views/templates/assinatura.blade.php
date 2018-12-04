<p style="text-align: center;"><img alt="" height="251" src="{{URL::to('/').$contas[0]->assinatura }}" width="255" /></p>


<p style="text-align: center;"><strong>Assinatura do secret&aacute;rio</strong><br />
    <strong>Nome completo </strong>: {{$contas[0]->name}}<br />
    <strong>CPF</strong>: {{$contas[0]->cpf}}<br />
    <strong>Data e hora da aprovação</strong>:{{$contas[0]->AprovadoEm}};<br />
    <strong>Secretaria</strong>:{{$contas[0]->descSecretaria}}<br />
    <strong>Numero Bancário</strong>:{{$contas[0]->NumeroBancario}}<br />
    <strong>Token de autenticação</strong><br />
    {{$contas[0]->token}}</p>

<p style="text-align: center;"><strong>&nbsp;</strong></p>

<p style="text-align: center;">&nbsp;</p>
