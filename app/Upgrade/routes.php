<?php

Route::get('comm-v1.9.6-to-pro-v1.9.19', ['as' => 'system.upgrade', 'uses' => 'App\Upgrade\Controllers\UpgradeController@upgrade']);

Route::get('pro-v1.9.19-to-pro-v1.9.20', ['as' => 'update-20', 'uses' => 'App\Upgrade\Controllers\UpgradeController@upgrade']);

Route::get('pro-v1.9.22-to-pro-v1.9.23', ['as' => 'update-20', 'uses' => 'App\Upgrade\Controllers\UpgradeController@upgrade']);

Route::get('pro-v1.9.31-to-pro-v1.9.32', ['as' => 'update-20', 'uses' => 'App\Upgrade\Controllers\UpgradeController@upgrade']);
