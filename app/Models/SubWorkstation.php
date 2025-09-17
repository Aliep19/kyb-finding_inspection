<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubWorkstation extends Model
{
    use HasFactory;

    protected $table = 'sub_workstations';
    protected $fillable = ['id_workstation', 'sub_name'];

    // Relasi: SubWorkstation milik 1 Workstation
    public function workstation()
    {
        return $this->belongsTo(Workstation::class, 'id_workstation');
    }

    // Relasi langsung ke Department lewat Workstation
    public function department()
    {
        return $this->hasOneThrough(
            Department::class,   // model tujuan
            Workstation::class,  // model perantara
            'id',                // id di workstations
            'id',                // id di departments
            'id_workstation',    // FK di sub_workstations
            'id_dept'            // FK di workstations
        );
    }
        public function defectInputs()
    {
        return $this->hasMany(DefectInput::class, 'sub_workstation_id');
    }
}
