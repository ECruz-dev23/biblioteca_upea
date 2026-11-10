<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusquedaLog extends Model
{
    protected $table = 'busquedas_log';
    protected $primaryKey = 'id_busqueda';
    public $timestamps = false;

    protected $fillable = ['termino'];
}