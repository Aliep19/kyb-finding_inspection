<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefectInputDetail extends Model
{
    protected $table = 'defect_input_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'defect_input_id','defect_category_id','defect_sub_id','jumlah_defect','keterangan'
    ];

    public function defectInput()
    {
        return $this->belongsTo(DefectInput::class, 'defect_input_id');
    }

    public function category()
    {
        return $this->belongsTo(DefectCategory::class, 'defect_category_id');
    }

    public function sub()
    {
        return $this->belongsTo(DefectSub::class, 'defect_sub_id');
    }
}

