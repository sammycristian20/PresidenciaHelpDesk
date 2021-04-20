<?php

namespace App\Plugins\Reseller\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Plugins\Reseller\Model\Reseller;
use App\Plugins\Reseller\Controllers\ResellerController;
use Auth;
//use Form;
use Hash;
use App\Model\plugin\reseller\Country;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use Exception;

class ResellerAuthController extends Controller
{
    public function PostLogin()
    {
        try {
            $request  = new Request();
            $this->CreateUserTable();
            $email    = \Input::get('email');
            $password = \Input::get('password');
            $reseller = new ResellerController;
            $club     = new Reseller();
            $club     = $club->where('id', '1')->first();
            if ($club) {
                $userid = $club->userid; //'619613';
                $apikey = $club->apikey; //'xr05pRZKUZJAiJUvdtrZgRr2TiXkDKFi';
                $field  = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
                $ip     = \Input::ip();
                //dd($ip);
                $user   = new User;
                //check user in faveo
                $user   = $user->where($field, $email)->first();
                if ($user && $user->role == 'admin') {
                    
                }
                //no user in faveo
                elseif (!$user) {
                    if ($club->enforce == 1) {
//                dd('not a user');
                        //check user in resller club
                        //dd($ip);
                        $check_in_reseller = $reseller->GenerateToken($ip, $userid, $apikey, $email, $password);
                        //yes user in reseller
                        if ((!is_array($check_in_reseller)) || ($check_in_reseller['status']
                                != 'ERROR')) {
                            //create the user in faveo
                            $user = $this->createUserInFaveo($email, $password);
                        }
                        //no user in reseller and faveo
                        else {
                            //Throw an error
                            throw new Exception("I can't find you, please register");
                        }
                    }
                }
                //yes such user in faveo
                else {

                    if ($user->role == 'user') {
                        if ($club->enforce == 1) {

                            //check in reseller
                            $token = $reseller->GenerateToken($ip, $userid, $apikey, $email, $password);
                            //dd($token);
                            // yes user in reseller 

                            if ((!is_array($token)) || ($token['status'] != 'ERROR')) {
                                //get the fields
                                $customer = $reseller->GetCustomerbyUserName($email, $userid, $apikey);
                                //update in user
                                $user     = $this->updateUser(json_decode($customer, true), $user, $password, true);
                            }
                            //no user in reseller club
                            else {

                                if ((preg_match('/\d/', $password) != TRUE) || (strlen($password)
                                        < 8)) {

                                    $password = self::getToken(8);
                                }
                                //dd($password);
                                $email    = $user->email;
                                $name     = $user->first_name;
                                //$company = $user->company;
                                //dd($password);
                                $signup   = $reseller->Signup($email, $userid, $apikey, $password
                                        = 'Tm3Nu1la', $name);
//                        dd($signup);
                                //$signup = $reseller->Signup($email, $userid, $apikey, $password1);
                                $customer = $reseller->GetCustomerbyUserName($email, $userid, $apikey);
                                if (is_array($customer) && key_exists('status', $customer)) {
                                    if ($customer['status'] == 'ERROR') {
                                        throw new Exception($customer['message']);
                                    }
                                }
                                $user = $this->updateUser(json_decode($customer, true), $user, $password, true);
                            }
                        }
                    }
                }
                if ($user && \Hash::check(\Input::get('password'), $user->getAuthPassword())) {
                    Auth::login($user);
                    return redirect('/');
                }
                else {
                    return redirect('/')->with('error', \Lang::get('lang.invalid'));
                }
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }
    public function FormRegister($flag)
    {

        $this->CreateUserTable();

        echo \View::make('reseller::registerform')->render();
        exit();
    }
    public function PostRegister(Request $request)
    {

        $this->validate($request, [
            'email'                 => 'required|max:50|email|unique:users',
            'full_name'             => 'required',
            'password'              => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password_confirmation' => 'required|same:password',
            'company'               => 'required',
            'address-line-1'        => 'required',
            'city'                  => 'required',
            'state'                 => 'required',
            //'country' => 'required',
            'zipcode'               => 'required',
            //'phone-cc' => 'required',
            'phone'                 => 'required',
            'lang-pref'             => 'required'
        ]);


        $user = new User();
        $club = new Reseller();


        $name     = $request->input('full_name');
        $email    = $request->input('email');
        $company  = $request->input('company');
        $address  = $request->input('address-line-1');
        $city     = $request->input('city');
        $state    = $request->input('state');
        $country  = $request->input('country');
        $zip      = $request->input('zipcode');
        $phonecc  = $request->input('phone-cc');
        $phone    = $request->input('phone');
        $lang     = $request->input('lang-pref');
        $password = $request->input('password');
        $club     = $club->where('id', '1')->first();
        if ($club->userid && $club->apikey) {
            $userid   = $club->userid; //'619613';
            $apikey   = $club->apikey; //'xr05pRZKUZJAiJUvdtrZgRr2TiXkDKFi';
            $reseller = new ResellerController;
            $signup   = $reseller->Signup($email, $userid, $apikey, $password, $name, $company, $address, $city, $state, 'IN', $zip, '+91', $phone, $lang);
            //dd($signup);
            if (is_array($signup) && key_exists('status', $signup)) {
                if ($signup['status'] == 'ERROR') {
                    if ($signup['message'] != "$email is already a Customer.") {
                        throw new Exception($signup['message']);
                    }
                }
            }
            $customer = $reseller->GetCustomerbyUserName($email, $userid, $apikey);

            if (is_array($customer) && key_exists('status', $customer)) {
                if ($customer['status'] == 'ERROR') {
                    throw new Exception($customer['message']);
                }
            }

            $this->updateUser($customer, $user, $password, true);
            return redirect('/')->with('success', 'Registered Successfully');
        }
        else {
            throw new Exception('Invalid Reseller settings');
        }
    }
    public function CreateUserTable()
    {
        if (!Schema::hasColumn('users', 'resellerid')) {
            Schema::table('users', function(Blueprint $table) {
                $table->string('address1');
                $table->string('city');
                $table->string('state');
                $table->string('country');
                $table->string('zip');
                $table->string('telnocc');
                $table->string('langpref');
                $table->string('creationdt');
                $table->string('pin');
                $table->string('stateid');
                $table->string('resellerid');
                $table->string('customerid');
                $table->string('mobilenocc');
                $table->string('salescontactid');
                $table->string('totalreceipts');
            });
        }
    }
    public function createUserInFaveo($email, $password)
    {
        try {
            $reseller = new ResellerController();
            $club     = new Reseller();
            $club     = $club->where('id', '1')->first();
            $userid   = $club->userid; //'619613';
            $apikey   = $club->apikey; //'xr05pRZKUZJAiJUvdtrZgRr2TiXkDKFi';
            $customer = $reseller->GetCustomerbyUserName($email, $userid, $apikey);
            $user     = new User();
            return $this->updateUser(json_decode($customer, true), $user, $password);
        } catch (Exception $ex) {
            //dd($ex);
        }
    }
    public function updateUser($customer, $user, $password, $reg = false)
    {
        try {
            if (is_array($customer) && in_array('ERROR', $customer)) {
                throw new Exception($customer['message']);
            }
            if (is_array($customer)) {
                $user = $user->updateOrCreate([
                    'user_name' => $customer['username'],
                    'email'     => $customer['username']
                ]);
                $mob  = null;
                if (checkArray('mobileno', $customer)) {
                    $mob = checkArray('mobileno', $customer);
                }
                $user->creationdt     = $customer['creationdt'];
                $user->langpref       = $customer['langpref'];
                $user->pin            = $customer['pin'];
                //$user->other_state = $customer['other_state'];
                $user->company        = $customer['company'];
                $user->mobile         = $mob; //$customer['mobileno'];
                //$user->customerstatus=$customer['customerstatus'];
                $user->stateid        = $customer['stateid'];
                $user->state          = $customer['state'];
                $user->city           = $customer['city'];
                $user->resellerid     = $customer['resellerid'];
                $user->customerid     = $customer['customerid'];
                $user->mobilenocc     = checkArray('mobilenocc', $customer);
                $user->salescontactid = $customer['salescontactid'];
                $user->telnocc        = $customer['telnocc'];
                $user->country        = $customer['country'];
                $user->totalreceipts  = $customer['totalreceipts'];
                $user->zip            = $customer['zip'];
                $user->address1       = $customer['address1'];
                $user->first_name     = $customer['name'];
                $user->role           = 'user';
                $user->active         = '1';

                //$pass = str_random(6);
                if ($reg) {
                    $password       = Hash::make($password);
                    $user->password = $password;
                }
                $user->save();
                return $user;
            }
        } catch (Exception $ex) {
            //dd($ex);
        }
    }
    public static function crypto_rand_secure($min, $max)
    {
        $range  = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log    = ceil(log($range, 2));
        $bytes  = (int) ($log / 8) + 1; // length in bytes
        $bits   = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        }
        while ($rnd >= $range);
        return $min + $rnd;
    }
    public static function getToken($length)
    {
        $token        = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max          = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[self::crypto_rand_secure(0, $max)];
        }
        return $token;
    }
    public function resetPassword()
    {
        try {
            $controller = new ResellerController();
            $resellers  = new Reseller();
            $reseller   = $resellers->find(1);
            $userid     = $reseller->userid;
            $apikey     = $reseller->apikey;
            $username   = \Input::get('email');
            $forgot     = $controller->ForgotPassword($userid, $apikey, $username);
            if (is_array($forgot) && key_exists('status', $forgot)) {
                if ($forgot['status'] == 'ERROR') {

                    throw new Exception($forgot['message']);
                }
            }
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
}
