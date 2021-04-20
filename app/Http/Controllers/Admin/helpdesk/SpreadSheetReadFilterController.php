<?php


namespace App\Http\Controllers\Admin\helpdesk;


use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class SpreadSheetReadFilterController implements IReadFilter
{
    private $startRow = 0;
    private $endRow   = 0;

    public function __construct($startRow, $endRow)
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
    }

    public function readCell($column, $row, $worksheetName = '') {

        if ($row >= $this->startRow && $row <= $this->endRow) {
            return true;
        }
        return false;
    }
}