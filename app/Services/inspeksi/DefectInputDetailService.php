<?php

namespace App\Services\Inspeksi;

use App\Models\DefectInput;
use App\Models\DefectInputDetail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DefectInputDetailService
{
    public function create(array $data): DefectInputDetail
    {
        return DefectInputDetail::create($data);
    }

    public function update(DefectInputDetail $detail, array $data): DefectInputDetail
    {
        // Ambil parent defect_input
        $defectInput = $detail->defectInput;

        // Hitung distribusi status repair/reject yang sudah ada
        $repairCount = $defectInput->details()->where('keterangan', 'repair')->count();
        $rejectCount = $defectInput->details()->where('keterangan', 'reject')->count();

        $repairTarget = $defectInput->repair ?? 0;
        $rejectTarget = $defectInput->reject ?? 0;

        $newStatus = $data['keterangan'] ?? null;

        // Cek jika user mencoba assign repair
        if ($newStatus === 'repair' && $repairCount >= $repairTarget && $detail->keterangan !== 'repair') {
            throw ValidationException::withMessages([
                'keterangan' => "Jumlah Repair sudah maksimal ({$repairTarget})."
            ]);
        }

        // Cek jika user mencoba assign reject
        if ($newStatus === 'reject' && $rejectCount >= $rejectTarget && $detail->keterangan !== 'reject') {
            throw ValidationException::withMessages([
                'keterangan' => "Jumlah Reject sudah maksimal ({$rejectTarget})."
            ]);
        }

        $detail->update($data);
        return $detail;
    }
 public function uploadPica(DefectInputDetail $detail, string $filePath): DefectInputDetail
{
    // Hapus PICA lama kalau ada
    if ($detail->pica) {
        Storage::disk('public')->delete($detail->pica);
    }

    // Update dengan path baru
    $detail->pica = $filePath;

    // Kalau belum pernah upload, set timestamp awal
    if (!$detail->pica_uploaded_at) {
        $detail->pica_uploaded_at = now();
    }

    $detail->save();

    return $detail;
}

}
