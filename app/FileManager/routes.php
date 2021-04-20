<?php

use App\FileManager\Listeners\FileManagerEventsListener;

Route::group([
     'middleware' => ['web','role.agent'],
     'prefix'     => "file-manager",
], function () {
    Route::get('initialize', '\App\FileManager\Controllers\FileManagerController@initialize')
    ->name('fm.initialize');

    Route::get('content', '\App\FileManager\Controllers\FileManagerController@content')
    ->name('fm.content');

    Route::get('tree', '\App\FileManager\Controllers\FileManagerController@tree')
    ->name('fm.tree');

    Route::get('select-disk', '\App\FileManager\Controllers\FileManagerController@selectDisk')
    ->name('fm.select-disk');

    Route::post('upload', '\App\FileManager\Controllers\FileManagerController@upload')
    ->name('fm.upload');

    Route::post('delete', '\App\FileManager\Controllers\FileManagerController@delete')
    ->name('fm.delete');

    Route::post('paste', '\App\FileManager\Controllers\FileManagerController@paste')
    ->name('fm.paste');

    Route::post('rename', '\App\FileManager\Controllers\FileManagerController@rename')
    ->name('fm.rename');

    Route::get('download', '\App\FileManager\Controllers\FileManagerController@download')
    ->name('fm.download');

    Route::get('thumbnails', '\App\FileManager\Controllers\FileManagerController@thumbnails')
    ->name('fm.thumbnails');

    Route::get('preview', '\App\FileManager\Controllers\FileManagerController@preview')
    ->name('fm.preview');

    Route::get('url', '\App\FileManager\Controllers\FileManagerController@url')
    ->name('fm.url');

    Route::post('create-directory', '\App\FileManager\Controllers\FileManagerController@createDirectory')
    ->name('fm.create-directory');

    Route::post('create-file', '\App\FileManager\Controllers\FileManagerController@createFile')
    ->name('fm.create-file');

    Route::post('update-file', '\App\FileManager\Controllers\FileManagerController@updateFile')
    ->name('fm.update-file');

    Route::get('stream-file', '\App\FileManager\Controllers\FileManagerController@streamFile')
    ->name('fm.stream-file');

    Route::post('zip', '\App\FileManager\Controllers\FileManagerController@zip')
    ->name('fm.zip');

    Route::post('unzip', '\App\FileManager\Controllers\FileManagerController@unzip')
    ->name('fm.unzip');

    Route::get('tinymce5', '\App\FileManager\Controllers\FileManagerController@tinymce5')
    ->name('fm.tinymce5');

    Route::get('files-info', '\App\FileManager\Controllers\FileManagerController@getFileInfo')
    ->name('fm.fileInfo');

    Route::get('inline-files-info', '\App\FileManager\Controllers\FileManagerController@getInlineFileInfo')
    ->name('fm.inline.fileInfo');

    //getUploadInformationFromPhpIni
    Route::get('upload-info', '\App\FileManager\Controllers\FileManagerController@getUploadInformationFromPhpIni')
        ->name('fm.upload.info');

    \Event::listen(
        'file-system-default-disk-change',
        function ($event) {
            (new FileManagerEventsListener())->pasteFilesAndDirectoriesOnDiskChange($event);
        }
    );

    \Event::listen(
        'App\FileManager\Events\FilesUploaded',
        function ($event) {
            (new FileManagerEventsListener())->handleFileManagerEvents($event, "file-upload");
        }
    );

    \Event::listen(
        'App\FileManager\Events\DirectoryCreated',
        function ($event) {
            (new FileManagerEventsListener())->handleFileManagerEvents($event, "directory-created");
        }
    );

    \Event::listen(
        'App\FileManager\Events\FileCreated',
        function ($event) {
            (new FileManagerEventsListener())->handleFileManagerEvents($event, "file-created");
        }
    );

    \Event::listen(
        'App\FileManager\Events\Paste',
        function ($event) {
            (new FileManagerEventsListener())->handleFileManagerEvents($event, "paste");
        }
    );

    \Event::listen(
        'App\FileManager\Events\Deleted',
        function ($event) {
            (new FileManagerEventsListener())->handleFileManagerEvents($event, "deleted");
        }
    );

    \Event::listen(
        'App\FileManager\Events\Rename',
        function ($event) {
            (new FileManagerEventsListener())->handleFileManagerEvents($event, "rename");
        }
    );
});
