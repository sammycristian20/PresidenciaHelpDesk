<?php

\Event::listen('settings.system',function(){
    $controller = new \App\FaveoStorage\Controllers\SettingsController();
    echo $controller->settingsIcon();
});


Route::group(['middleware'=>['web']],function(){

    Route::get('storage',['as'=>'storage','uses'=>'App\FaveoStorage\Controllers\SettingsController@settings'])
            ->middleware(['roles']);

    Route::post('storage',['as'=>'post.storage','uses'=>'App\FaveoStorage\Controllers\SettingsController@postSettings'])
            ->middleware(['roles']);

    Route::get('attachment',['as'=>'attach','uses'=>'App\FaveoStorage\Controllers\SettingsController@attachment']);

    Route::post("img_upload/",'App\FaveoStorage\Controllers\StorageController@ckEditorUpload');

    Route::get("api/thumbnail/{hashId}",'App\FaveoStorage\Controllers\StorageController@getThumbnail');

    Route::get("api/thumbnail-by-path", 'App\FaveoStorage\Controllers\StorageController@getThumbnailByPath');

    Route::get("api/view-attachment/{hashId}", 'App\FaveoStorage\Controllers\StorageController@viewAttachment');

    Route::get("api/download-attachment/{hashId}", 'App\FaveoStorage\Controllers\StorageController@downloadAttachment');

    Route::post('api/tiny-image-uploader', 'App\FaveoStorage\Controllers\StorageController@uploadHandlerForTinyMce');
});

