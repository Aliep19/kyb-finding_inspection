<?php

namespace App\Models\Lembur;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CtUser extends Authenticatable
{
    protected $connection = 'lembur'; // DB lembur
    protected $table = 'ct_users_hash';
    protected $guarded = [];
    public $timestamps = true;
}
