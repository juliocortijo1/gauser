<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Secretaria extends Model
{
    public $timestamps = false;
    protected $table = 'tblSecretaria';
    protected $fillable = [
        'id',
        'descSecretaria'];

}

