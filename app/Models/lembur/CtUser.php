<?php

namespace App\Models\Lembur;

use Illuminate\Foundation\Auth\User as Authenticatable;

class CtUser extends Authenticatable
{
    protected $connection = 'lembur'; // DB lembur
    protected $table = 'ct_users_hash';

    protected $fillable = [
        'full_name', 'pwd', 'approved', 'npk', 'dept', 'sect', 'subsect', 'golongan', 'acting'
    ];

    // supaya Laravel tahu kolom password-nya apa
    public function getAuthPassword()
    {
        return $this->pwd;
    }

    // mapping role berdasarkan golongan + acting
    public function getRoleAttribute()
    {
        if ($this->golongan == 4 && $this->acting == 2) {
            return 'spv';
        } elseif ($this->golongan == 4 && $this->acting == 1) {
            return 'manager';
        } elseif ($this->golongan == 3) {
            return 'foreman';
        } else {
            return 'staff';
        }
    }
}
