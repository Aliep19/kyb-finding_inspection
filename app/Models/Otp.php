<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $connection = 'inspeksi'; // DB default
    protected $table = 'otp';
    protected $guarded = [];
    public $timestamps = false; // created_at manual, tapi expired_at ada
}
