<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    public $timestamps = false;
    protected $table = 'tblContas';
    protected $fillable = [
        'idContrato',
        'statusConta',
        'valorConta',
        'vencimentoConta',
        'pdfContaEndereco',
        'criado_em',
        'IdCriador',
        'ModificadoEm',
        'IdModificador',
        'IdContestacao',
        'IdAprovador',
        'AprovadoEm',
        'NumeroBancario'];
}
