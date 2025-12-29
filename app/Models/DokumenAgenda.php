<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenAgenda extends Model
{
    protected $connection = 'mysql_agenda_online';
    protected $table = 'dokumens';
}
