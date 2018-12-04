<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public $timestamps = false;
    protected $table = 'tblAcao';
    protected $fillable = [
        'tipoAcao',
        'objetoAcao',
        'idUsuarioAcao',
        'dataAcao'
    ];




}
