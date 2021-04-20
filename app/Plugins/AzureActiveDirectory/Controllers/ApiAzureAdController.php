<?php

namespace App\Plugins\AzureActiveDirectory\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\helpdesk\Common\DataTableRequest;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Traits\NavigationHelper;
use App\Traits\UserImport;
use App\User;
use Auth;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Plugins\AzureActiveDirectory\Model\AzureAd;
use App\Plugins\AzureActiveDirectory\Request\AzureAdRequest;
use App\Plugins\SyncPluginToLatestVersion;
use Exception;
use Illuminate\Support\Collection;
use Lang;
use Logger;
use Cache;

class ApiAzureAdController extends Controller
{
    use UserImport, NavigationHelper;

    /**
     * @var AzureAd
     */
    private $azureAdSettings;

    /**
     * @var AzureConnector
     */
    private $azureConnector;

    public function __construct(AzureConnector $azureConnector)
    {
        $this->middleware('role.admin')->except(['authenticate', 'authenticationCallback', 'getMetaSettings']);

        (new SyncPluginToLatestVersion)->sync('AzureActiveDirectory');

        $this->azureConnector = $azureConnector;
    }

    public function create()
    {
        return view('AzureActiveDirectory::settings-create');
    }

    public function edit($azureAdId)
    {
        return view('AzureActiveDirectory::settings-create');
    }

    public function index()
    {
        return view('AzureActiveDirectory::settings-index');
    }

    /**
     * Saves azure settings
     * @param AzureAdRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAzureAdSettings(AzureAdRequest $request)
    {
        try {
            Cache::forget('azure_meta_settings');

            $this->setAzureConfigurations($this->getAzureAdObject($request));

            $accessToken = $this->azureConnector->getAccessCodeInClientCredentialsMode();

            if ($request->import) {
                $users = $this->azureConnector->getUsers($accessToken);

                $initialCount = User::count();

                $this->handleBulk($users);

                $finalCount = User::count();

                // total n users found, m new users created
                $newCreatedUsers = $finalCount - $initialCount;

                $totalUsersFound = count($users);

                $this->azureAdSettings->save();

                return successResponse(
                    Lang::get('AzureActiveDirectory::lang.azure_import_message', ['totalUsers'=>$totalUsersFound, 'newUsers'=> $newCreatedUsers]),
                    ['azure_ad_id'=> $this->azureAdSettings->id]
                );
            }

            $this->azureAdSettings->save();
            return successResponse(Lang::get('lang.saved_successfully'), ['azure_ad_id'=> $this->azureAdSettings->id]);
        } catch (ClientException $e) {
            $response = $e->getResponse();

            $responseBody = json_decode($response->getBody()->getContents());

            $errorMessage = isset($responseBody->error_description) ? $responseBody->error_description: $responseBody->error;

            return errorResponse($errorMessage);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Gets azure object after populating it with the request
     * @param $request
     */
    private function getAzureAdObject($request)
    {
        $azureAdObject = AzureAd::find($request->id) ? : new AzureAd;
        $azureAdObject->app_name = $request->app_name;
        $azureAdObject->tenant_id = $request->tenant_id;
        $azureAdObject->app_id = $request->app_id;
        $azureAdObject->app_secret = $request->app_secret;
        $azureAdObject->login_button_label = $request->login_button_label;
        return $azureAdObject;
    }

    /**
     * Gets azure ad settings
     * @param $azureId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAzureAdSettings($azureAdId)
    {
        if ($azureAd = AzureAd::find($azureAdId)) {
            return successResponse('', $azureAd);
        }
        return errorResponse(Lang::get('lang.not_found'), 404);
    }

    /**
     * Gets azure ad settings
     * @param $azureId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAzureAdSettings($azureAdId)
    {
        Cache::forget('azure_meta_settings');
        if ($azureAd = AzureAd::find($azureAdId)) {
            $azureAd->delete();
            return successResponse(Lang::get('lang.successfully_deleted'));
        }
        return errorResponse(Lang::get('lang.not_found'), 404);
    }

    public function authenticate($azureAdId)
    {
        if ($azureAD = AzureAd::find($azureAdId)) {
            $this->azureConnector->setAzureAdSettings($azureAD);
            return $this->azureConnector->authenticate();
        }
        return errorResponse(Lang::get('lang.not_found'), 404);
    }

    /**
     * Authentication callback
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function authenticationCallback(Request $request)
    {
        $expectedState = session('oauthState');

        $azureAdId = session('azureAdId');

        $request->session()->forget('oauthState');

        $request->session()->forget('azureAdId');

        $providedState = $request->query('state');

        if (!isset($expectedState)) {
            return redirect('/');
        }

        if (!isset($providedState) || $expectedState != $providedState) {
            return redirect('500');
        }

        $this->azureConnector->setAzureAdSettings(AzureAd::find($azureAdId));

        // Authorization code should be in the "code" query param
        $authCode = $request->query('code');
        try {
            $accessToken = $this->azureConnector->getAccessCodeInAuthorizationCodeMode($authCode);

            // use this token to get user data
            $user = $this->azureConnector->getMyData($accessToken);

            // create this is user. User import integration takes over from here
            // passing single user as array so that it can be created
            $this->handleBulk([$user]);

            // get that user using its username from the database
            $importIdentifier = $this->getAttributeValue('import_identifier', $user);

            $user = User::where('import_identifier', $importIdentifier)->first();

            if ($user) {
                Auth::login($user, true);
                return redirect('/');
            }

            return redirect('404');
        } catch (Exception $e) {
            Logger::exception($e);
            return redirect('404');
        }
    }

    /**
     * Sets azure configuration for the whole flow to use
     * @param AzureAd $azureAd
     */
    private function setAzureConfigurations(AzureAd $azureAd)
    {
        $this->azureAdSettings = $azureAd;
        $this->azureConnector->setAzureAdSettings($azureAd);
    }

    /**
     * Appends meta data of azure to passed variable
     * @param $data
     */
    public function appendMetaSettings(&$data)
    {
        $metaData = Cache::rememberForever('azure_meta_settings', function () {

             $azureSettings = AzureAd::select('id', 'login_button_label')->orderBy('id', 'desc')->get()->map(function ($element) {
                $azureObj = (object)[];
                $azureObj->login_url = url("azure-active-directory/authenticate/$element->id");
                $azureObj->login_button_label = $element->login_button_label;
                return $azureObj;
             });
            $hideDefaultLogin = (bool)CommonSettings::where('option_name', 'hide_default_login')->where('optional_field', 'azure')->value('option_value');

            return ['hide_default_login'=> $hideDefaultLogin, 'directory_settings'=> $azureSettings];
        });

        $data['azure_meta_settings'] = $metaData;
    }

    /**
     * gets list of all directories
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAzureAdList(DataTableRequest $request)
    {
        $searchQuery = $request->search_query ?: '';

        $limit = $request->limit ?: 10;

        $sortOrder = $request->sort_order ? : 'desc';

        $sortField = $request->sort_field ? : 'id';

        $azureAds = AzureAd::where('app_name', 'LIKE', "%$searchQuery%")
            ->orWhere('app_id', 'LIKE', "%$searchQuery%")
            ->select('id', 'app_name', 'app_id', 'updated_at', 'created_at')
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit);

        $hideDefaultLogin = (bool)CommonSettings::where('option_name', 'hide_default_login')->where('optional_field', 'azure')->value('option_value');

        return successResponse('', ['directories'=> $azureAds, 'hide_default_login'=> $hideDefaultLogin]);
    }

    /**
     * gets attribute value for azure AD
     * @param $attribute
     * @param $userObjectArray
     * @return string|null|string[]
     */
    protected function getAttributeValue($attribute, $userObjectArray)
    {
        // displayName => first_name
        // surname => last_name
        // id => import_identifier
        // userPrincipalName => user_name
        // jobTitle => undecided
        // mail => email
        // mobilePhone => mobile??
        // officeLocation => undecided
        // preferredLanguage => undecided

        $thirdPartyAttribute = $this->getThirdPartyAttributeByFaveoAttribute($attribute);

        if (isset($userObjectArray[$thirdPartyAttribute])) {
            return $userObjectArray[$thirdPartyAttribute];
        }

        switch ($attribute) {
            case 'department':
            case 'organization':
            case 'org_dept':
                return [];

            case 'role':
                return 'user';

            default:
                return '';
        }
    }

    protected function isOverwriteAllowed($attribute): bool
    {
        // making it false for everything for now
       if ($attribute == 'import_identifier') {
           return true;
       }
        return false;
    }

    protected function getThirdPartyAttributeByFaveoAttribute($faveoAttribute): ?string
    {
        $mapper = [
            'user_name'=> 'userPrincipalName',
            'first_name'=> 'givenName',
            'last_name'=> 'surname',
            'import_identifier'=> 'id',
            'email'=> 'mail'
        ];

        if (isset($mapper[$faveoAttribute])) {
            return $mapper[$faveoAttribute];
        }
        return '';
    }

    /**
     * Injects navigation into sidebar
     * @param Collection $coreNavigationContainer
     */
    public function injectAzureAdminNavigation(Collection &$coreNavigationContainer)
    {
        $navigationArray = collect();

        $navigationArray->push(
            $this->getNavigationObject(
                Lang::get("AzureActiveDirectory::lang.azure_configuration_settings"),
                'fa fa-cork',
                'azure-active-directory/settings',
                'azure-active-directory/settings'
            )
        );

        $coreNavigationContainer->push(
            $this->createNavigationCategory(Lang::get('AzureActiveDirectory::lang.configuration_settings'), $navigationArray)
        );
    }

    /**
     * Hides default login
     * @param Request $request
     * @return JsonResponse
     */
    public function hideDefaultLogin(Request $request)
    {
        if(!AzureAd::count()){
            return errorResponse(Lang::get('AzureActiveDirectory::lang.please_configure_atleast_one_directory'));
        }

        Cache::forget('azure_meta_settings');

        CommonSettings::updateOrCreate(['option_name'=> 'hide_default_login', 'optional_field'=> 'azure'], ['option_value'=> (bool)$request->hide_default_login]);

        return successResponse(Lang::get('lang.updated_successfully'));
    }
}
