<?php

namespace App\Services\Common;

use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Generate unique concurrency-safe document numbers.
     * e.g., generate('TKT', 'tickets', 'ticket_number') -> TKT-2026-000001
     *       generate('INV', 'invoices', 'invoice_number') -> INV-2026-000001
     *       generate('QTN', 'quotations', 'quotation_number') -> QTN-2026-000001
     *       generate('CLT', 'client_profiles', 'client_number') -> CLT-2026-000001
     *       generate('PRJ', 'projects', 'project_number') -> PRJ-2026-000001
     *       generate('RCT', 'payments', 'receipt_number') -> RCT-2026-000001
     */
    public static function generate(string $prefix, string $table, string $column): string
    {
        return DB::transaction(function () use ($prefix, $table, $column) {
            $year = date('Y');
            $searchPrefix = "{$prefix}-{$year}-";

            // Lock records matching prefix in descending order
            $latest = DB::table($table)
                ->where($column, 'LIKE', "{$searchPrefix}%")
                ->lockForUpdate()
                ->orderBy($column, 'desc')
                ->value($column);

            if ($latest) {
                $lastNumber = (int) substr($latest, strlen($searchPrefix));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            return sprintf('%s%06d', $searchPrefix, $nextNumber);
        });
    }
}
