<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageModel extends Model
{
    public $table = 'log_comrades_bot';

    protected $fillable = ['idUserLine', 'message'];

    protected $primaryKey = 'id';
}
