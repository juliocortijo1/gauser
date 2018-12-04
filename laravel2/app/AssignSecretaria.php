<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignSecretaria extends Model
{
    public $timestamps = false;
    protected $table = 'tblSecretariaParaUsuario';
    protected $fillable = [
        'idUsuario',
        'idSecretaria'];
}
