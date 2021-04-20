<?php

use Illuminate\Foundation\Inspiring;
use App\Model\helpdesk\Settings\System;

//use Exception;
//use DB;

/*
  |--------------------------------------------------------------------------
  | Console Routes
  |--------------------------------------------------------------------------
  |
  | This file is where you may define all of your Closure based console
  | commands. Each Closure is bound to a command instance allowing a
  | simple approach to interacting with each command's IO methods.
  |
 */
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

/**
 * Command for pre install check
 */
Artisan::command('preinsatall:check', function () {
    try {
        $check_for_pre_installation = System::select('id')->first();
        if ($check_for_pre_installation) {

            throw new \Exception('The data in database already exist. Please provide fresh database', 100);
        }
    } catch (\Exception $ex) {
        if ($ex->getCode() == 100) {
            $this->call('droptables');
        }
        //throw new \Exception($ex->getMessage());
    }
    $this->info('Preinstall has checked successfully');
})->describe('check for the pre installation');


/**
 * Migration for installation
 */
Artisan::command('install:migrate', function () {
    try {
        $tableNames = \Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

        if (count($tableNames) == 0) {
            $this->call('migrate', ['--force' => true]);
        }
    } catch (Exception $ex) {
        throw new \Exception($ex->getMessage());
    }
    $this->info('Migrated successfully');
})->describe('migration for install');


/**
 * Seeding for installation
 */
Artisan::command('install:seed', function () {
    \Schema::disableForeignKeyConstraints();
    $tableNames = \Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
    foreach ($tableNames as $name) {
        if ($name == 'migrations') {
            continue;
        }
        \DB::table($name)->truncate();
    }
    $this->call('db:seed', ['--force' => true]);
    $this->info('seeded successfully');
})->describe('Seeding for install');

Artisan::command('faveo:encode {value}', function($value) {

    $enc = App\Http\Controllers\Utility\LibraryController::encryptByFaveoPublicKey($value);
    $this->info("Actual value    : $value");
    $this->info("Encrypted value : $enc");
});

Artisan::command('download:faveo', function() {
    try {
        $tmpFile  = storage_path('zip.zip');
        $resource = fopen($tmpFile, 'w');
        $stream   = \GuzzleHttp\Psr7\stream_for($resource);
        $client   = new \GuzzleHttp\Client();

        $url     = "https://www.faveohelpdesk.com/billing/public/download/faveo";
        $data    = [
            'domain'       => App\Http\Controllers\Utility\LibraryController::encryptByFaveoPublicKey('localhost'),
            'order_number' => App\Http\Controllers\Utility\LibraryController::encryptByFaveoPublicKey('80404697'),
            'serial_key'   => App\Http\Controllers\Utility\LibraryController::encryptByFaveoPublicKey('JH7IYZNM3LJEQQHZ'),
        ];
        $options = [
            \GuzzleHttp\RequestOptions::SINK            => $stream, // the body of a response
            \GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => 10.0, // request
            \GuzzleHttp\RequestOptions::TIMEOUT         => 60.0, // response
            'form_params'                   => $data,
            'verify'                        => true,
        ];
        $response = $client->request('POST', $url, $options);
        $stream->close();
    } catch (GuzzleHttp\Exception\RequestException $e) {
        echo \GuzzleHttp\Psr7\str($e->getRequest());
        if ($e->hasResponse()) {
            echo \GuzzleHttp\Psr7\str($e->getResponse());
        }
    } catch (\Exception $e) {
        dd($e);
    }
});

Artisan::command('faveo:decode', function() {
    $value = '{"seal":"gaRJdC+ou4pAlRj+eYQS1QA1OCRqBJCp76q0rg+VArocjxjeuz6Ypb6BTd55sKRBc5ExsDtGWIQk","envelope":"ihKBoogefLZvInUguN+sua6aAlOZ9nhCntRew5cJOYxHdKNdVBgYIYTk5s1yAz+CZvJaAyrTMJN4Ywec3HhaR2QCxuAMFdUWS0wu8cjVx6ufUvwxdr+y00TVtYwj6fAQEDiJfEhePHXf+fjcJlmjMFu2mJmVZScxoWMZZGgKwlhwelrfRZe5OhtS3wdLK768u+2sFzfta9Vj4YnBWyUC0v2QNkKHBwwt6zCayt7ZSBYtqX6667dJ\/gkAgEk1FqD+qarXYStfRKSGTcO8MS5l55hUzH3ooJoLsJNK8WO9w7ZBiT41zhPZGM63+wwz4DJ65rROzb6w8r0KZC1dFNa9BA=="}';
    $dec   = App\Http\Controllers\Utility\LibraryController::decryptByFaveoPrivateKey($value);
    $this->info("Encrypted value    : $value");
    $this->info("Decrypted value    : $dec");
});
