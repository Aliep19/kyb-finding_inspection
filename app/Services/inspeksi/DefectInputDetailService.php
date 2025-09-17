<?php

namespace App\Services\Inspeksi;

use App\Models\DefectInputDetail;

class DefectInputDetailService
{
    public function create(array $data): DefectInputDetail
    {
        return DefectInputDetail::create($data);
    }

    public function update(DefectInputDetail $detail, array $data): DefectInputDetail
    {
        $detail->update($data);
        return $detail;
    }
}
