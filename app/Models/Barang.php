<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $primaryKey = "id_brg";
    protected $table = "tb_barang";
    public $guarded = [];
}