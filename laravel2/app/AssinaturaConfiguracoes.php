<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssinaturaConfiguracoes extends Model
{
    public $timestamps = false;
    protected $table = 'tblAssinaturaConf';
    protected $fillable = [
        'idUsuario',
        'assinatura',
        'cpf'];
}
