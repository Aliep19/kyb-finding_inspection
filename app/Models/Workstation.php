<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workstation extends Model
{
    use HasFactory;

    protected $table = 'workstations';
    protected $fillable = ['id_dept', 'sect_name'];

    // Relasi: Workstation milik 1 Department
    public function department()
    {
        return $this->belongsTo(Department::class, 'id_dept');
    }

    // Relasi: 1 Workstation punya banyak SubWorkstation
    public function subWorkstations()
    {
        return $this->hasMany(SubWorkstation::class, 'id_workstation');
    }
}
