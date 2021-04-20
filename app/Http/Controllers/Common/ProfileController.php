<?php 

namespace App\Http\Controllers\Common;

use App\Facades\Attach;
use App\Http\Controllers\Controller;
use App\Http\Requests\helpdesk\ProfileRequest;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Model\helpdesk\Utility\CountryCode;
use Illuminate\Http\Request;
use Input;
use Lang;
use Auth;
use Session;
use App\Model\kb\Comment;


class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['postUserLanguage']);
    }


    /**
     * @deprecated
     * Saves/updates profile data.
     * @param ProfileRequest $request
     * @return Response
     */
    public function postProfile(ProfileRequest $request)
    {
        try {
            $user = Auth::user();
            //validation is done in ProfileRequest
            if($request->input('country_code')){
               $code = CountryCode::select('phonecode')->where('phonecode', $request->input('country_code'))->get();
                if (!count($code)) {
                    return errorResponse(['mobile' => Lang::get('lang.invalid_country_code')]);
                }
                $user->country_code = $request->input('country_code');
            }
            $user->fill($request->except('profile_pic', 'mobile','phone_number'));
            if (Input::file('profile_pic')) {
                $logoFileName = Attach::put('profile', Input::file('profile_pic'), null, null, true, 'public');
                $user->profile_pic = Attach::getUrlForPath($logoFileName, null, 'public');
            }

           $user->mobile = $request->get('mobile') ? $request->get('mobile') : NULL;

           $user->phone_number = ($request->get('phone_number') && $request->get('phone_number')!='null') ? $request->get('phone_number') : NULL;

            if(!$user->save()){
            return errorResponse(Lang::get('lang.fails'));
            }
            //update comment profile pic
            Comment::where('email', Auth::user()->email)->update(['profile_pic'=>Auth::user()->profile_pic]);

            return successResponse(Lang::get('lang.Profile-Updated-sucessfully'));

        } catch (Exception $e) {
            return errorResponse(Lang::get($e->getMessage()));
        }
    }


    /**
     * Updates user language in DB
     * @return Response          updates  use language in the DB
     */
    public function postUserLanguage(Request $request)
    {
        $languageCode = $request->input('locale');

        //check for language validation
        $appPath = base_path();
        $availableLanguages = array_map('basename', \File::directories("$appPath/resources/lang"));
        if(!in_array($languageCode, $availableLanguages)){
            return errorResponse(Lang::get('lang.invalid_language'));
        }
        //if user is not logged in, language should get updated
        if(!Auth::check()){
            Session::put('language', $languageCode);
            return successResponse(Lang::get('lang.updated_successfully'));
        }

        //saving user language
        Auth::user()->user_language = $languageCode;
        if(Auth::user()->save()){
            return successResponse(Lang::get('lang.updated_successfully'));
        }
         return errorResponse(Lang::get('lang.fails'));
    }

}

?>