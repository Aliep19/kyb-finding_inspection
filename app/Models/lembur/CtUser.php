<?php

namespace App\Models\Lembur;

use Illuminate\Database\Eloquent\Model;

class CtUser extends Model
{
    protected $connection = 'lembur'; // DB lembur
    protected $table = 'ct_users_hash';
    protected $guarded = [];
    public $timestamps = true;
}
