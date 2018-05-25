<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $table = 'log_comrades_bot';

    protected $fillable = ['idUser', 'idMessage', 'message'];

    protected $primaryKey = 'id';
}
