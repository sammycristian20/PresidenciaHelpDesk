<?php

Route::get('licenseError', function(){
 return view('errors.licenseError');
});

Route::get('api/licenseError', ['as' => 'licenseError', function () {
	
	$host = \Config::get('database.connections.mysql.host');
        $username = \Config::get('database.connections.mysql.username');
        $password = \Config::get('database.connections.mysql.password');
        $database = \Config::get('database.connections.mysql.database');
	   $GLOBALS["mysqli"]=mysqli_connect($host, $username, $password, $database); //establish connection to MySQL database
	   $license_notifications_array=  aplVerifyLicense($GLOBALS["mysqli"]);
        $notificationText = $license_notifications_array['notification_text'];

        if(strpos(strtolower($notificationText), "license is not installed yet or corrupted") !== false){
           $notificationText = $notificationText. helpMessage("license_is_not_installed_yet_or_corrupted");
       }

	   return errorResponse($notificationText);
}]);

Route::post('licenseVerification', 'Auth\AuthController@licenseVerification')->name('licenseVerification');
