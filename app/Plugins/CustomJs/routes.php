<?php
\Event::listen('timeline-customjs', function ($mode) {
        $controllers = new App\Plugins\CustomJs\Controllers\HandleEventsController();
        return $controllers->handle($mode);
});

Route::group(['middleware' => ['web', 'auth', 'roles']], function () {
	Route::get('custom-js/settings', ['as' => 'customjs-settings', 'uses' => 'App\Plugins\CustomJs\Controllers\SettingsController@settings']);
	Route::get('get-custom-js', ['as' => 'get-cusotom-js', 'uses' => 'App\Plugins\CustomJs\Controllers\SettingsController@getJsTable']);

	Route::get('custom-js/create', ['as' => 'customjs.create', 'uses' => 'App\Plugins\CustomJs\Controllers\SettingsController@create']);

	Route::get('custom-js/get-routes', ['as' => 'fetch.routes', 'uses' => 'App\Plugins\CustomJs\Controllers\SettingsController@getRoutes']);

	Route::post('custom-js/create', ['as' => 'customjs.create', 'uses' => 'App\Plugins\CustomJs\Controllers\SettingsController@store']);

	Route::get('custom-js/edit/{id}', ['as' => 'customjs.edit', 'uses' => 'App\Plugins\CustomJs\Controllers\SettingsController@edit']);
	Route::patch('custom-js/edit/{id}', ['as' => 'customjs.edit', 'uses' => 'App\Plugins\CustomJs\Controllers\SettingsController@update']);

	Route::get('custom-js/delete/{id}', ['as' => 'customjs.delete', 'uses' => 'App\Plugins\CustomJs\Controllers\SettingsController@delete']);
});
