<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'target_value',
        'start_month',
        'start_year',
        'end_month',
        'end_year',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getPeriodAttribute()
    {
        $months = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        return $months[$this->start_month]." ".$this->start_year." - ".
               $months[$this->end_month]." ".$this->end_year;
    }
    // App\Models\Target.php

public function scopeSearch($query, $search)
{
    if ($search) {
        $query->where('target_value', 'like', "%$search%")
              ->orWhereHas('department', function ($q) use ($search) {
                  $q->where('dept_name', 'like', "%$search%");
              });
    }
    return $query;
}
}
