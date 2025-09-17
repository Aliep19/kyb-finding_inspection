<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefectCategory extends Model
{
    protected $fillable = ['defect_name', 'jenis_defect'];
    protected $table = 'defect_categories';

    public function subs()
    {
        return $this->hasMany(DefectSub::class, 'id_defect_category');
    }
}
