<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefectSub extends Model
{
    protected $fillable = ['id_defect_category', 'jenis_defect'];
    protected $table = 'defect_subs';

    public function category()
    {
        return $this->belongsTo(DefectCategory::class, 'id_defect_category');
    }
}
