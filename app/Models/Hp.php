<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hp extends Model
{
    protected $connection = 'isd'; // DB isd
    protected $table = 'hp';
    protected $guarded = [];
    public $timestamps = false; // Tidak ada timestamps di tabel
}
