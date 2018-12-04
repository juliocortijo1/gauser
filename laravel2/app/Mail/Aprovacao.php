<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Aprovacao extends Mailable
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
    public function __construct($numeroExtrato,$DescContrato)
    {
        $this->numeroExtrato = $numeroExtrato;
        $this->DescContrato = $DescContrato;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('gdc.prefeitura.itu@netserv.com.br')
            ->subject('Aprovação do Extrato.'.$this->numeroExtrato)
            ->view('contestacoes.corpo_aprovacao');
    }
}
