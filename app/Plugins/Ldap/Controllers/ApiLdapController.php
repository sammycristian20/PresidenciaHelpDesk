<?php

namespace App\Plugins\Ldap\Controllers;

use App\Exceptions\DuplicateUserException;
use App\Http\Requests\helpdesk\Common\DataTableRequest;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Plugins\Ldap\Model\Ldap;
use App\Plugins\Ldap\Model\LdapAdAttribute;
use App\Plugins\Ldap\Model\LdapFaveoAttribute;
use App\Plugins\Ldap\Model\LdapSearchBase;
use App\Plugins\Ldap\Request\DirectoryAttributeRequest;
use App\Plugins\Ldap\Request\LdapAdvancedSettingsRequest;
use App\Plugins\Ldap\Request\LdapSettingsRequest;
use App\Plugins\Ldap\Request\SearchBaseRequest;
use App\Model\helpdesk\Form\FormField;
use App\Plugins\SyncPluginToLatestVersion;
use App\Repositories\FormRepository;
use App\User;
use Auth;
use Cache;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Lang;
use Logger;

/**
 * Contains all the API connector functions for Ldap
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class ApiLdapController extends BaseLdapController
{

    public function __construct(LdapConnector $ldapConnector)
    {
        $this->middleware('role.admin')->except(['appendMetaSettings']);

        // syncing ldap to latest version
        (new SyncPluginToLatestVersion)->sync('Ldap');

        //setting ldap connector configuration
        //create a setter method here and call it before setting $this->config
        $this->ldapConnector = $ldapConnector;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('Ldap::settings-index');
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('Ldap::settings-create');
    }

    /**
     * Enrty to blade file for Ldap settings
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        if (!Ldap::whereId($id)->count()) {
            return redirect('404');
        }

        return view('Ldap::settings-edit', compact('id'));
    }

    /**
     * Gets Ldap settings in required format inluding search bases
     * @return JsonResponse
     */
    public function getLdapSettings($id)
    {
        $this->ldapConfig = Ldap::whereId($id)->with('searchBases:id,ldap_id,search_base,filter,user_type,department_ids,organization_ids')->first();

        if (!$this->ldapConfig) {
            return errorResponse(Lang::get('lang.not_found'), 404);
        }

        //if role is selectable or not (if value is anything other that `FAVEO DEFAULT`, then it shouldn't)
        $this->ldapConfig['show_role'] = $this->getThirdPartyAttributeByFaveoAttribute('role') == 'FAVEO DEFAULT';

        //if organization is selectable or not (if value is anything other that `FAVEO DEFAULT`, then it shouldn't)
        $this->ldapConfig['show_organization'] = ($this->ldapConfig['show_role'] && $this->getThirdPartyAttributeByFaveoAttribute('organization') == 'FAVEO DEFAULT');

        //if department is selectable or not (if value is anything other that `FAVEO DEFAULT`, then it shouldn't)
        $this->ldapConfig['show_department'] = ($this->ldapConfig['show_role'] && $this->getThirdPartyAttributeByFaveoAttribute('department') == 'FAVEO DEFAULT');

        foreach ($this->ldapConfig->searchBases as &$searchBase) {
            $searchBase->departments = Department::whereIn('id', $searchBase->department_ids)->select('id', 'name')->get();
            $searchBase->organizations = Organization::whereIn('id', $searchBase->organization_ids)->select('id', 'name')->get();
            unset($searchBase->department_ids, $searchBase->organization_ids);
        }

        $warningMessage = '';

        // check if ldap is configured properly, if not, warning message
        if (!$this->ldapConfig->is_valid) {
            $warningMessage = Lang::get('Ldap::lang.please_configure_ldap');
        }

        // check if ldap extension is enabled, if not, warning message
        if (!$this->ldapConnector->isLdapExtensionEnabled()) {
            $warningMessage = Lang::get('Ldap::lang.ldap_extension_not_enabled');
        }

        return successResponse($warningMessage, $this->ldapConfig);
    }

    /**
     * Gets Ldap meta settings data
     * NOTE: will be used at the time of login to show fields on the UI
     * @return JsonResponse
     */
    public function appendMetaSettings(&$data)
    {
        $ldapMetaSettings = Cache::rememberForever('ldap_meta_settings', function () {
            $ldapMetaSettings = Ldap::select('id', 'ldap_label', 'forgot_password_link')->get();
            return ['hide_default_login'=> $this->isDefaultLoginHidden(), 'directory_settings'=> $ldapMetaSettings];
        });

        $data['ldap_meta_settings'] = $ldapMetaSettings;
    }

    /**
     * Checks Ldap for authentication for the given user
     * @param array $request associative array of request with all login parameters
     * @return boolean
     */
    public function authLdap($request, &$validLdap2faCredential)
    {
        $username = isset($request['email']) ? $request['email'] : null;
        $password = isset($request['password']) ? $request['password'] : null;
        $is_ldap_auth = isset($request['ldap']) ? $request['ldap'] : null;
        $ldapId = isset($request['ldap_id']) ? $request['ldap_id'] : null;
        $remember = isset($request['remember']) ? $request['remember'] : false;

        $ldapSetting = Ldap::find($ldapId);

        if ($is_ldap_auth && $ldapSetting) {
            $this->setLdapConfig(Ldap::find($ldapId));

            // getting full username after appending prefix and suffix to username
            $username = $this->ldapConfig->prefix . $username . $this->ldapConfig->suffix;

            // instead of is valid credentials, get the username of created user
            // get ldap user instance then call createOrUpdateUser
            if ($this->ldapConnector->isValidCredentials($username, $password)) {
                // getting username format which is compatible with faveo username
                $username = $this->ldapConnector->getFormattedUsername($username);

                $user = User::where('user_name', $username)->where('active', 1)->first();

                if ($user) {
                    Auth::login($user, $remember);
                    if (Auth::user()->google2fa_secret && Auth::user()->is_2fa_enabled ==1) {
                        $validLdap2faCredential = true;
                        return $validLdap2faCredential;
                    }
                    return true;
                }
            }
            throw new Exception(Lang::get('lang.invalid_credentials'));
        }
        return false;
    }

    public function verifyCredentials($password, &$passwordVerified)
    {
        $username = Auth::user()->user_name;
        if (Auth::user()->import_identifier && $this->ldapConnector->isValidCredentials($username, $password)) {
            $passwordVerified = true;
            return $passwordVerified;
        }
    }

    /**
     * handles Saving of ldap settings after authenticating it
     * @param LdapSettingsRequest $request
     * @return JsonResponse
     */
    public function postLdapSettings(LdapSettingsRequest $request)
    {
        //clearing the meta cache so update the new data
        Cache::forget('ldap_meta_settings');

        try {
            $this->setLdapConfig($this->getLdapObject($request));

            //check ldap connection using credentials before saving. Only for authentication purpose
            $isValidLdapCredentials = $this->ldapConnector->isValidLdapConfig();

            if ($isValidLdapCredentials) {
                $this->ldapConfig->is_valid = true;
                $this->ldapConfig->save();
                return successResponse(Lang::get('lang.successfully_saved'), ['ldap_id'=> $this->ldapConfig->id]);
            }
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Sets Ldap config for the class into $ldapConfig property
     * @param $ldapConfig Ldap
     * @return null
     */
    protected function setLdapConfig(Ldap $ldapConfig)
    {
        // need ld
        //updating config of this class
        $this->ldapConfig = $ldapConfig;

        // TEMPORARIALY diverting ldapconnector calls to ldapConnectorOld, if schema is open_ldap or free_ipa
        // As OpenLDAP has issues in Adldap2 package
        if ($ldapConfig->schema == 'open_ldap' || $ldapConfig->schema == 'free_ipa') {
            $this->ldapConnector = new LdapConnectorOld;
        }

        //setting ldap configurations in the connector
        $this->ldapConnector->setLdapConfig(
            $ldapConfig->domain,
            $ldapConfig->username,
            $ldapConfig->password,
            $ldapConfig->port,
            $ldapConfig->encryption,
            $ldapConfig->schema
        );
    }

    /**
     * Gets ldap object after filling the object
     * @param LdapSettingsRequest $request
     * @return Ldap
     */
    private function getLdapObject(LdapSettingsRequest $request): Ldap
    {
        $ldapConfig = $request->id ? Ldap::find($request->id) : new Ldap;

        $ldapConfig->fill([
            'domain' => $request->input('domain'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'port' => $request->input('port'),
            'encryption' => $request->input('encryption'),
            'schema' => $request->input('schema') ? $request->input('schema') : 'active_directory',
            'ldap_label' => (string)$request->input('ldap_label'),
            'forgot_password_link' => (string)$request->input('forgot_password_link'),
            'prefix' => (string)$request->input('prefix'),
            'suffix' => (string)$request->input('suffix'),
        ]);

        return $ldapConfig;
    }

    /**
     * Autheticates search bases, imports user based on that
     * @param SearchBaseRequest $request
     * @return JsonResponse
     */
    public function postSearchBases($ldapId, SearchBaseRequest $request)
    {
        // it recieves array of search bases which is looped into importing users
        $searchBases = $request->input('search_bases');
        $import = $request->input('import');

        if (!($ldap = Ldap::whereId($ldapId)->first())) {
            return errorResponse(Lang::get('lang.not_found'), 404);
        }

        $this->setLdapConfig($ldap);

        $userCreated = 0;
        try {
            //import all users and save
            foreach ($searchBases as $searchBase) {
                //check if record already exists
                $searchBaseObj = $searchBase['id'] ? LdapSearchBase::find($searchBase['id']) : new LdapSearchBase;

                $searchBaseObj->fill(['search_base' => $searchBase['search_base'], 'filter' => $searchBase['filter'],
                    'user_type' => $searchBase['user_type'], 'department_ids' => $searchBase['department_ids'],
                    'organization_ids' => $searchBase['organization_ids'], 'ldap_id' => $ldap->id
                ]);

                if ($import) {
                    $userCreated += $this->handleUserImportBySearchBase($searchBaseObj);
                }

                $searchBaseObj->save();
            }
            // users before creating
            $message = $import ? "$userCreated new users imported from the directory" : Lang::get('lang.successfully_saved');

            return successResponse($message);
        } catch (DuplicateUserException $e) {
            return errorResponse($e->getMessage() . helpMessage("ldap_user_duplication"));
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * imports user from LDAP server and creates them in faveo and returns the count of new users imported
     * @param LdapSearchBase $searchBaseObj
     * @return int
     */
    private function handleUserImportBySearchBase(LdapSearchBase $searchBaseObj)
    {
        $importedUsersCount = 0;

        $usersBeforeImport = User::count();

        $users = $this->importBySearchBasis($searchBaseObj, $importedUsersCount);

        $this->createValidUsers($searchBaseObj, $users);

        return User::count() - $usersBeforeImport;
    }

    /**
     * Pings Ldap server with given search base
     * @param Request $request
     * @return Response
     */
    public function pingLdapWithSearchQuery(int $ldapId, Request $request)
    {
        $ldapConfig = Ldap::find($ldapId);

        if (!$ldapConfig) {
            return Lang::get('lang.not_found');
        }

        $this->setLdapConfig($ldapConfig);

        $searchBaseObj = new LdapSearchBase;
        $searchBaseObj->search_base = $request->input('search_base');
        $searchBaseObj->filter = $request->input('filter');
        $foundUsers = 0;

        try {
            $this->importBySearchBasis($searchBaseObj, $foundUsers);
            return successResponse("$foundUsers users found with this query");
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Deletes a search base
     * @param int $id search base id that has to be deleted
     * @return Response
     */
    public function deleteSearchBase($id)
    {
        $ldapSearchBase = LdapSearchBase::find($id);
        if ($ldapSearchBase) {
            $ldapSearchBase->delete();
            return successResponse(Lang::get('Ldap::lang.search_base_deleted_successfully'));
        }
        return errorResponse(Lang::get('Ldap::lang.search_base_not_found'));
    }

    /**
     * Import users based on the search queries stored in the database.
     * It is meant for cron command. Logs are wriiten accordingly
     * @param Ldap $ldap
     * @return null
     */
    public function importByCurrentConfiguration(Ldap $ldap)
    {
        $this->setLdapConfig($ldap);
        $searchBases = LdapSearchBase::all();
        $importedUsersCount = 0;
        $usersBeforeImport = User::count();

        foreach ($searchBases as $searchBase) {
            try {
                $users = $this->importBySearchBasis($searchBase, $importedUsersCount);
                $this->createValidUsers($searchBase, $users);
            } catch (Exception $e) {
                //if any error occurs, we don't terminate but log that error and contniue thw proecess
                Logger::exception($e);
                return false;
            }
        }
        $userCreatedAfterImport = User::count() - $usersBeforeImport;
        loging('ldap', "$importedUsersCount total users found on Ldap server, $userCreatedAfterImport new users created in the database", 'info');
        return true;
    }

    /**
     * Gets LDAP advanced settings
     * @param int $ldapId
     * @return JsonResponse
     */
    public function getAdvancedSettings(int $ldapId)
    {
        if (!($ldap = Ldap::find($ldapId))) {
            return errorResponse(Lang::get('lang.not_found'));
        }

        // custom field handling
        $ldap->faveoAttributes()->where('name', 'LIKE', "custom_%")->pluck('name');

        $defaultAdAttributeId = $ldap->adAttributes()->where('name', 'FAVEO DEFAULT')->value('id');

        FormRepository::getUserCustomFieldList()->map(function ($formField) use ($ldap, $defaultAdAttributeId) {
            $ldap->faveoAttributes()->firstOrCreate(['name'=>"custom_$formField->id"], ['name'=>"custom_$formField->id", "mapped_to"=> $defaultAdAttributeId,
                'editable'=>1]);
        });

        $ldapFaveoAttributeQuery = $ldap->faveoAttributes()
            ->select('id', 'name', 'mapped_to', 'overwrite', 'editable', 'overwriteable');

        //unset org_dept if org_dept module is disabled
        if (!isOrgDeptModuleEnabled()) {
            $ldapFaveoAttributeQuery->where('name', '!=', 'org_dept');
        }

        $ldapFaveoAttributes = $ldapFaveoAttributeQuery->orderBy('id')->get();

        $ldapAdAttributes = $ldap->adAttributes()->select('id', 'name', 'is_loginable')->get();

        //get data from ldap_faveo_attributes
        $data = ['faveo_attributes' => $ldapFaveoAttributes, 'third_party_attributes' => $ldapAdAttributes];
        return successResponse('', $data);
    }

    /**
     * Posts Ldap advanced settings
     * @param LdapAdvancedSettingsRequest $request [description]
     * @param int $ldapId
     * @return JsonResponse
     */
    public function postAdvancedSettings(LdapAdvancedSettingsRequest $request, int $ldapId)
    {
        try {
            if (!($ldap = Ldap::find($ldapId))) {
                return errorResponse(Lang::get('lang.not_found'));
            }

            //post data to ldap_faveo_attributes
            $ldapFaveoAttributes = $request->faveo_attributes;

            foreach ($ldapFaveoAttributes as $attribute) {
                $ldap->faveoAttributes()->updateOrCreate(['id' => $attribute['id']], $attribute);
            }
            return successResponse(Lang::get('lang.successfully_saved'));
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * gets list of ldap server directory attributes
     * @param DataTableRequest $request
     * @param int $ldapId
     * @return JsonResponse
     */
    public function getDirectoryAttribute(DataTableRequest $request, int $ldapId)
    {
        try {
            if (!($ldap = Ldap::find($ldapId))) {
                return errorResponse(Lang::get('lang.not_found'));
            }

            //if search query is not defined
            $searchString = $request->input('search_query') ?: '';
            $sortField = $request->input('sort_field') ?: 'updated_at';
            $sortOrder = $request->input('sort_order') ?: 'desc';
            $limit = $request->input('limit') ?: 10;

            $baseQuery = $ldap->adAttributes()->where('name', 'LIKE', "%$searchString%");

            // allow search and sort
            $adAttributes = $baseQuery->orderBy($sortField, $sortOrder)->paginate($limit);

            return successResponse('', $adAttributes);
        } catch (Exception $e) {
            // it is for  frontend API, so no langauge required
            return errorResponse('invalid parameters given', 500);
        }
    }

    /**
     * updates or create a new directory attribute record
     * @param DirectoryAttributeRequest $request
     * @param int $ldapId
     * @return JsonResponse
     */
    public function postDirectoryAttribute(DirectoryAttributeRequest $request, int $ldapId)
    {
        $name = $request->name;
        $id = $request->id;

        //check if record already exists
        $adAttributeObj = $id ? LdapAdAttribute::find($id) : new LdapAdAttribute;

        if ($adAttributeObj && $adAttributeObj->is_default) {
            return errorResponse(Lang::get('lang.cannot_edit_default_attribute'));
        }

        $adAttributeObj->name = $name;
        $adAttributeObj->is_default = false;
        $adAttributeObj->ldap_id = $ldapId;
        $adAttributeObj->save();

        return successResponse(Lang::get('lang.updated_successfully'));
    }

    /**
     * Deletes directory attribute record
     * @return Response
     */
    public function deleteDirectoryAttribute($id)
    {
        $adAttribute = LdapAdAttribute::where('id', $id)->where('is_default', 0)->first();

        if (!$adAttribute) {
            return errorResponse(Lang::get('Ldap::lang.record_not_found'));
        }

        $adAttribute->delete();
        // all faveo attributes using this attribute should revert back to FAVEO DEFAULT

        $faveoDefaultId = LdapAdAttribute::where('ldap_id', $adAttribute->ldap_id)
            ->where('name', 'FAVEO DEFAULT')->value('id');

        // updating existing entries which were using this attribute to use FAVEO DEFAULT
        LdapFaveoAttribute::where('ldap_id', $adAttribute->ldap_id)->where('mapped_to', $id)->update(['mapped_to' => $faveoDefaultId]);

        return successResponse(Lang::get('Ldap::lang.deleted_successfully'));
    }

    /**
     * Gets list LDAP directory settings available
     * @return JsonResponse
     */
    public function getLdapSettingsList()
    {
        $ldapList = Ldap::select('id', 'schema', 'username', 'encryption', 'domain', 'port')
            ->orderBy('created_at', 'desc')
            ->get();
        $ldapList->transform(function ($element) {
            $element->image_url = $this->getImageUrl($element->schema);
            return $element;
        });

        return successResponse('', ['ldap_list'=>$ldapList, 'hide_default_login'=> $this->isDefaultLoginHidden()]);
    }

    /**
     * @param $schema
     * @return Application|\Illuminate\Contracts\Routing\UrlGenerator|string
     * @throws Exception
     */
    public function getImageUrl($schema)
    {
        switch ($schema) {
            case 'active_directory':
                return url('/themes/default/common/images/active-directory-ldap.png');

            case 'free_ipa':
                return url('/themes/default/common/images/free_ipa.png');

            case 'open_ldap':
                return url('/themes/default/common/images/open_ldap.png');

            default:
                throw new Exception('Unsupported schema '.$schema);
        }
    }

    /**
     * Deletes passed Ldap settings
     * @param int $ldapId
     */
    public function deleteLdapSettings(int $ldapId)
    {
        $ldapSetting = Ldap::find($ldapId);

        if (!$ldapSetting) {
            return errorResponse(Lang::get('lang.not_found'), 404);
        }

        $ldapSetting->delete();

        //clearing the meta cache so update the new data
        Cache::forget('ldap_meta_settings');

        return successResponse(Lang::get('lang.deleted_successfully'));
    }

    /**
     * Hides default login
     * @param Request $request
     * @return JsonResponse
     */
    public function hideDefaultLogin(Request $request)
    {
        if(!Ldap::count()){
            return errorResponse(Lang::get('Ldap::lang.please_configure_atleast_one_directory'));
        }

        Cache::forget('ldap_meta_settings');

        CommonSettings::updateOrCreate(['option_name'=> 'hide_default_login', 'optional_field'=> 'ldap'], ['option_value'=> (bool)$request->hide_default_login]);

        return successResponse(Lang::get('lang.updated_successfully'));
    }

    /**
     * tells if default login is hidden
     * @return bool
     */
    private function isDefaultLoginHidden()
    {
        return (bool)CommonSettings::where('option_name', 'hide_default_login')->where('optional_field', 'ldap')->value('option_value');
    }
}
