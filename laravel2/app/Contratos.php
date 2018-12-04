<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contratos extends Model
{
    public $timestamps = false;
    protected $table = 'tblContratos';
    protected $fillable = [
        'idFornecedor',
        'descContrato',
        'identContratoExt',
        'statusContrato',
        'idSecretaria',
        'endCopiaContrato',
        ];

}
