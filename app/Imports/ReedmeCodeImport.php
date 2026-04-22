<?php

namespace App\Imports;

use App\Models\ReedmeCode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ReedmeCodeImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Normalize keys to lowercase to handle any capitalization
        $row = array_change_key_case($row, CASE_LOWER);

        $code = trim($row['code'] ?? '');
        $status = trim($row['status'] ?? 'active');

        // Skip empty codes
        if ($code === '') {
            return null;
        }

        // Skip duplicate codes
        if (ReedmeCode::where('code', $code)->exists()) {
            return null;
        }

        return new ReedmeCode([
            'code'       => $code,
            'status'     => $status,
            'created_at' => now(),
        ]);
    }
}
