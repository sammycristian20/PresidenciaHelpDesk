<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//forgot password
Route::post('password/email', 'Auth\PasswordController@postEmail');

//reset password
Route::post('password/reset', 'Auth\PasswordController@passwordReset');


//activate account
Route::get('account/activate/{token}', 'Auth\AuthController@accountActivate');

//gets active social login providers
Route::get('active-providers', 'Auth\AuthController@getActiveSocialLoginsList');

//gets country code
Route::get('country-code','Client\helpdesk\GuestController@getCountryCode');

Route::get('mobile/dependency/{type}','Common\Dependency\DependencyController@handle')->middleware('jwt.auth');

// it should return token
Route::post('login', function(\App\Http\Requests\helpdesk\LoginRequest $request){

  // even LDAP login will work like charm here
  $response = (new \App\Http\Controllers\Auth\AuthController)->postLogin($request);
  // if user is logged in, we will send the token, else send the response
  if(Auth::user()){
    $token = Auth::user()->createToken('MyApp')->accessToken;
    return successResponse('',['token'=> $token]);
  }
  return $response;
});

Route::get('api/dependency/{type}', 'Common\Dependency\DependencyController@handle')->middleware('auth:api');
