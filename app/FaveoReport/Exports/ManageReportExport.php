<?php

namespace App\FaveoReport\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class ManageReportExport implements WithMultipleSheets
{
    private $rows;

    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    /**
     * @inheritDoc
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new ManagementReportSheetExport($this->rows);

        return $sheets;
    }
}