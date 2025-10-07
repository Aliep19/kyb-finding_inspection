<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DefectInputDetail extends Model
{
    protected $table = 'defect_input_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'defect_input_id', 'defect_category_id', 'defect_sub_id', 'jumlah_defect', 'keterangan', 'pica', 'pica_uploaded_at'
    ];
    protected $casts = [
        'pica_uploaded_at' => 'datetime',
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

    // Helper: Cek apakah bisa edit PICA (reusable di controller & view)
    public function canEditPica(int $lockMinutes = 30): bool
{
    if (!$this->pica || !$this->pica_uploaded_at) {
        return true;
    }

    $now = now();
    $uploadedAt = $this->pica_uploaded_at;

    // Hitung selisih waktu dalam menit dari waktu upload ke sekarang
    $diffMinutes = $uploadedAt->diffInMinutes($now, false);

    // Untuk debug
    Log::info("Check Lock | ID: {$this->id} | Upload: {$uploadedAt} | Now: {$now} | Diff: {$diffMinutes}");

    // Jika sudah lewat batas menit, maka terkunci
    return $diffMinutes <= $lockMinutes;
}

}
