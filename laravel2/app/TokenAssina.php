<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TokenAssina extends Model
{
    public $timestamps = false;
    protected $table = 'tblToken';
    protected $fillable = [
        'id',
        'idConta',
        'Navegador',
        'SO',
        'IP',
        'Token'];
}
