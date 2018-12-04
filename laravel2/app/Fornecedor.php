<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    public $timestamps = false;
    protected $table = 'tblFornecedor';
    protected $fillable = [
        'tipoPessoa',
        'nomeFantasia',
        'telFornecedor',
        'emailForncedor',
        'userSistema'];

}
