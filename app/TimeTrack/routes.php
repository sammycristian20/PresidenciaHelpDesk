<?php

// Define routes
Route::group(['middleware' => ['web', 'auth']], function () {

    Route::group(['middleware' => ['role.agent']], function () {
        Route::get('time-track', ['as' => 'timetrack.setting', 'uses' => 'App\TimeTrack\Controllers\SettingsController@showSetting']);

        Route::get('time-track/get-settings', ['as' => 'timetrack.setting.data', 'uses' => 'App\TimeTrack\Controllers\SettingsController@getTimeTrackSettings']);

        Route::post('time-track', ['as' => 'timetrack.settings.store', 'uses' => 'App\TimeTrack\Controllers\SettingsController@storeSetting']);

        Route::group(['prefix' => 'ticket'], function() {
            Route::get('{ticket}/time-track/{timeTrack?}', ['as' => 'ticket.timetrack.getbyticket', 'uses' => 'App\TimeTrack\Controllers\TimeTrackController@getTrackedTimes']);

            Route::post('{ticket}/time-track/{timeTrack?}', ['as' => 'ticket.timetrack.store', 'uses' => 'App\TimeTrack\Controllers\TimeTrackController@storeTimeTrack']);

            Route::delete('time-track/{timeTrack}', ['as' => 'ticket.timetrack.destroy', 'uses' => 'App\TimeTrack\Controllers\TimeTrackController@destroyTimeTrack']);
        });
    });

});


// injecting navigation into admin ticket navigation
Event::listen('admin-panel-tickets-navigation-data-dispatch', function(&$navigationObject){
  $timeTrackNavigation = (new \App\Http\Controllers\Common\Navigation\Navigation);
  $timeTrackNavigation->name = \Lang::get('timetrack::lang.time-track');
  $timeTrackNavigation->iconClass = 'fa fa-clock-o';
  $timeTrackNavigation->setRedirectUrl('time-track');
  $timeTrackNavigation->routeString = 'time-track';
  $navigationObject->injectChildNavigation($timeTrackNavigation);
});
