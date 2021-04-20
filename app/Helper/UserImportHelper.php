<?php


namespace App\Helper;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class UserImportHelper implements WithChunkReading, WithStartRow
{
    use Importable;

    /**
     * @var int
     */
    private $headingRow;

    /**
     * @param int $headingRow
     */
    public function __construct(int $headingRow = 1)
    {
        $this->headingRow = $headingRow;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return $this->headingRow;
    }
}