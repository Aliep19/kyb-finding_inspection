<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefectInput extends Model
{
    protected $table = 'defect_inputs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_defect','tgl','shift','npk','line','marking_number','lot',
        'kayaba_no','total_check','ok','total_ng','reject','repair'
    ];

    public function details()
    {
        return $this->hasMany(DefectInputDetail::class, 'defect_input_id');
    }
    public function subWorkstation()
    {
        return $this->belongsTo(SubWorkstation::class, 'sub_workstation_id');
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\lembur\CtUser::class, 'npk', 'npk');
    }
}

