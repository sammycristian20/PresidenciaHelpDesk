<?php

namespace App\Plugins\Migration\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use Exception;
use App\Plugins\Migration\Controllers\ImportCsv;
use Response;
use App\Plugins\Migration\Controllers\SpiceworkMigration;

class MigrateController extends Controller {

    public function getMigration() {
        return view('migration::migration');
    }

    public function migrate() {
        $app = \Input::get('app');
        switch ($app) {
            case "osTicket":
                $import = new OsTicketMigration();
                return $import->import();
            case "osticket":
                $import = new OsTicketMigration();
                return $import->import();
            case "Spicework":
                $import = new SpiceworkMigration();
                return $import->import();
            case "spicework":
                $import = new SpiceworkMigration();
                return $import->import();
        }
    }

    public function upload() {
        $app = 'osticket';
        $dir = storage_path();
        try {
            $request = new \Flow\Request();
            $destination = $this->fileName($dir, $app, $request);
            $config = new \Flow\Config();
            $config->setTempDir($dir.'/chunks_temp_folder');
            $file = new \Flow\File($config);
            $response = Response::make('fails', 200);
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!$file->checkChunk()) {
                    return Response::make('', 404);
                }
            } else {
                if ($file->validateChunk()) {
                    $file->saveChunk();
                } else {
                    return Response::make('failed', 400);
                }
            }
            if ($file->validateFile() && $file->save($destination)) {
                $response = Response::make('success', 200);
            }
            return $response;
        } catch (\Exception $e) {
            dd($e);
            $result = $e->getMessage();
            return response()->json(compact('result'), 500);
        }
    }

    public function filename($dir, $app, $request) {
        $name = 'ost_ticket.csv';
        if ($app == 'osticket') {
            $name = 'ost_ticket.csv';
        }
        $destination = $dir . DIRECTORY_SEPARATOR . $request->getFileName();
        if (\File::exists($destination)) {
            unlink($destination);
        }
        return $destination;
    }

    public function setConfig($dir) {
        $config = new \Flow\Config();
        $temp_folder = $dir . DIRECTORY_SEPARATOR . 'chunk';
        \File::makeDirectory($temp_folder, 0775, true, true);
        $config->setTempDir($temp_folder);
        return $config;
    }

}
