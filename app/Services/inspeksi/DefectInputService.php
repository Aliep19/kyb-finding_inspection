<?php

namespace App\Services\Inspeksi;

use App\Models\DefectInput;

class DefectInputService
{
    public function generateUniqueCode(): string
    {
        return 'DF-' . now()->format('YmdHis') . '-' . rand(100, 999);
    }

    public function calculateFields(array $data): array
    {
        $totalCheck = (int) ($data['total_check'] ?? 0);
        $totalNg = (int) ($data['total_ng'] ?? 0);

        $data['ok'] = $data['ok'] ?? max($totalCheck - $totalNg, 0);
        $data['reject'] = $data['reject'] ?? 0;
        $data['repair'] = $data['repair'] ?? 0;

        return $data;
    }

    public function create(array $data): DefectInput
    {
        $data['id_defect'] = $this->generateUniqueCode();
        $data = $this->calculateFields($data);

        return DefectInput::create($data);
    }

    public function update(DefectInput $defectInput, array $data): DefectInput
    {
        $data = $this->calculateFields($data);
        $defectInput->update($data);

        return $defectInput;
    }
}
