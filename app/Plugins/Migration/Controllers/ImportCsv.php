<?php

namespace App\Plugins\Migration\Controllers;

use Maatwebsite\Excel\Files\ExcelFile;
use Input;

class ImportCsv extends ExcelFile {

    public function getFile() {
        $file = storage_path('ost_ticket.csv');//Input::get('file');
        return $file;
    }

    public function getFilters() {
        return [
            'chunk'
        ];
    }

}
