<?php

namespace App\Helper;

use Maatwebsite\Excel\Concerns\FromArray;

class UserExportHelper implements FromArray
{
    public $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }
}
