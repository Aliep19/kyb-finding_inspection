<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['dept_name'];

    // Relasi: 1 Department punya banyak Workstation
    public function workstations()
    {
        return $this->hasMany(Workstation::class, 'id_dept');
    }
}
