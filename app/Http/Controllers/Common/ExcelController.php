<?php

namespace App\Http\Controllers\Common;

use App\Helper\UserExportHelper;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel;
use Exception;

class ExcelController extends Controller {

    public function export($filename, $data) {
        if(count($data)==0){
            throw new Exception('No data');
        }

        $headingRow = reset($data);

        array_unshift($data, array_keys($headingRow));

        return Excel::download(new UserExportHelper($data), "$filename.xlsx");
//        dd($data);
//        Excel::create($filename, function($excel) use($data){
//            $excel->sheet('sheet', function($sheet) use($data) {
//                $sheet->fromArray($data);
//            });
//        })->export('xls');
    }

}
