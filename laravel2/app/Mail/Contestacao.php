<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Contestacao extends Mailable
{
    use Queueable, SerializesModels;
    public $numeroExtrato;
    public $DescContrato;
    public $descricao;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($numeroExtrato,$DescContrato,$descricao)
    {
        $this->numeroExtrato = $numeroExtrato;
        $this->DescContrato = $DescContrato;
        $this->descricao = $descricao;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('gdc.prefeitura.itu@netserv.com.br')
            ->subject('Contestação de extrato.'.$this->numeroExtrato)
            ->view('contestacoes.corpo_contestacao');
    }
}
